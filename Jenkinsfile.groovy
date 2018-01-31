#!/usr/bin/env groovy

node {
  def FAILURE = null
  def IMAGE_VERSION = null
  def SCM_VARS = [:]
  def USGS_IMAGES = "${GITLAB_INNERSOURCE_REGISTRY}/devops/containers"

  // WS Variables
  def WS_APP_NAME = 'earthquake-geoserve-ws'
  def WS_BUILD_IMAGE = "${USGS_IMAGES}/node:latest"
  def WS_FROM_IMAGE = "${USGS_IMAGES}/httpd-php:latest"
  def WS_LOCAL_IMAGE = "local/${WS_APP_NAME}:latest"
  def WS_DEPLOY_IMAGE = "${GITLAB_INNERSOURCE_REGISTRY}/ghsc/hazdev/earthquake-geoserve/ws"
  def WS_PENTEST_CONTAINER = "${WS_APP_NAME}-PENTEST"

  // DB Variables
  def DB_APP_NAME = 'earthquake-geoserve-db'
  def DB_FROM_IMAGE = "${USGS_IMAGES}/library/mdillon/postgis:9.6"
  def DB_LOCAL_IMAGE = "local/${DB_APP_NAME}:latest"
  def DB_DEPLOY_IMAGE = "${GITLAB_INNERSOURCE_REGISTRY}/ghsc/hazdev/earthquake-geoserve/db"

  // Runs zap.sh as daemon and used to execute zap-cli calls within
  def OWASP_CONTAINER = "${WS_APP_NAME}-${BUILD_ID}-OWASP"
  def OWASP_IMAGE = "${USGS_IMAGES}/library/owasp/zap2docker-stable"


  try {
    stage('Update') {
      // Start from scratch
      cleanWs()

      // Checkout latest and set some variables
      SCM_VARS = checkout scm

      if (GIT_BRANCH != '') {
        // Check out the specified branch
        sh "git checkout --detach ${GIT_BRANCH}"

        // Update relevant SCM_VARS
        SCM_VARS.GIT_BRANCH = GIT_BRANCH
        SCM_VARS.GIT_COMMIT = sh(
          returnStdout: true,
          script: "git rev-parse HEAD"
        )
      }

      // Determine image tag to use
      if (SCM_VARS.GIT_BRANCH != 'origin/master') {
        IMAGE_VERSION = SCM_VARS.GIT_BRANCH.split('/').last().replace(' ', '_')
      } else {
        IMAGE_VERSION = 'latest'
      }
    }

    stage('Build Images') {
      def info = [:]
      def pkgInfo = readJSON file: 'package.json'

      info.version = pkgInfo.version
      info.branch = SCM_VARS.GIT_BRANCH
      info.commit = SCM_VARS.GIT_COMMIT
      info.image = IMAGE_VERSION

      // Convert from Map --> JSON
      info = readJSON text: groovy.json.JsonOutput.toJson(info)
      writeJSON file: 'dist/metadata.json', pretty: 4, json: info

      // Build candidate WS image for later penetration testing
      sh """
        docker pull ${WS_BUILD_IMAGE}
        docker pull ${WS_FROM_IMAGE}
        docker build \
          --build-arg BUILD_IMAGE=${WS_BUILD_IMAGE} \
          --build-arg FROM_IMAGE=${WS_FROM_IMAGE} \
          -f Dockerfile-ws \
          -t ${WS_LOCAL_IMAGE} \
          .
      """

      // Build candidate DB image for
      sh """
        docker pull ${DB_FROM_IMAGE}
        docker build \
          --build-arg FROM_IMAGE=${DB_FROM_IMAGE} \
          -t ${DB_LOCAL_IMAGE} \
          -f Dockerfile-db \
          .
      """
    }

    stage('Unit Tests') {
      echo 'TODO :: Add unit tests to web service and database';
      // // Note that running angular tests destroys the "dist" folder that was
      // // originally created in Install stage. This is not needed later, so
      // // okay, but just be aware ...

      // // Run linting, unit tests, and end-to-end tests
      // docker.image(TESTER_IMAGE).inside () {
      //   ansiColor('xterm') {
      //     sh """
      //       ng lint
      //       ng test --single-run --code-coverage --progress false
      //       ng e2e --progress false
      //     """
      //   }
      // }

      // // Publish results
      // cobertura(
      //   autoUpdateHealth: false,
      //   autoUpdateStability: false,
      //   coberturaReportFile: '**/cobertura-coverage.xml',
      //   conditionalCoverageTargets: '70, 0, 0',
      //   failUnhealthy: false,
      //   failUnstable: false,
      //   lineCoverageTargets: '80, 0, 0',
      //   maxNumberOfBuilds: 0,
      //   methodCoverageTargets: '80, 0, 0',
      //   onlyStable: false,
      //   sourceEncoding: 'ASCII',
      //   zoomCoverageChart: false
      // )
    }

    stage('Penetration Tests') {
      def ZAP_API_PORT = '8090'
      def OWASP_REPORT_DIR = "${WORKSPACE}/owasp-data"


      // Ensure report output directory exists
      sh """
        if [ ! -d "${OWASP_REPORT_DIR}" ]; then
          mkdir -p ${OWASP_REPORT_DIR}
          chmod 777 ${OWASP_REPORT_DIR}
        fi
      """

      // Start a container to run penetration tests against
      sh """
        docker run --rm --name ${WS_PENTEST_CONTAINER} \
          -d ${WS_LOCAL_IMAGE}
      """

      // Start a container to execute OWASP PENTEST
      sh """
        docker run --rm -d -u zap \
          --name=${OWASP_CONTAINER} \
          --link=${WS_PENTEST_CONTAINER}:application \
          -v ${OWASP_REPORT_DIR}:/zap/reports:rw \
          -i ${OWASP_IMAGE} \
          zap.sh \
          -daemon \
          -port ${ZAP_API_PORT} \
          -config api.disablekey=true
      """

      // Wait for OWASP container to be ready, but not for too long
      timeout(
        time: 20,
        unit: 'SECONDS'
      ) {
        echo 'Waiting for OWASP container to finish starting up'
        sh """
          set +x
          status='FAILED'
          while [ \$status != 'SUCCESS' ]; do
            sleep 1;
            status=`\
              (\
                docker exec -i ${OWASP_CONTAINER} \
                  curl -I localhost:${ZAP_API_PORT} \
                  > /dev/null 2>&1 && echo 'SUCCESS'\
              ) \
              || \
              echo 'FAILED'\
            `
          done
        """
      }

      // Run the penetration tests
      ansiColor('xterm') {
        sh """
          PENTEST_IP='application'

          docker exec ${OWASP_CONTAINER} \
            zap-cli -v -p ${ZAP_API_PORT} spider \
            http://\$PENTEST_IP/

          docker exec ${OWASP_CONTAINER} \
            zap-cli -v -p ${ZAP_API_PORT} active-scan \
            http://\$PENTEST_IP/

          docker exec ${OWASP_CONTAINER} \
            zap-cli -v -p ${ZAP_API_PORT} report \
            -o /zap/reports/owasp-zap-report.html -f html

          docker stop ${OWASP_CONTAINER} ${WS_PENTEST_CONTAINER}
        """
      }

      // Publish results
      publishHTML (target: [
        allowMissing: true,
        alwaysLinkToLastBuild: true,
        keepAll: true,
        reportDir: OWASP_REPORT_DIR,
        reportFiles: 'owasp-zap-report.html',
        reportName: 'OWASP ZAP Report'
      ])
    }

    stage('Publish Image') {
      IMAGE_VERSION = 'latest'

      // Determine image tag to use
      if (SCM_VARS.GIT_BRANCH != 'origin/master') {
        IMAGE_VERSION = SCM_VARS.GIT_BRANCH.split('/').last().replace(' ', '_')
      }

      // Re-tag candidate image as actual image name and push actual image to
      // repository
      docker.withRegistry(
        "https://${GITLAB_INNERSOURCE_REGISTRY}",
        'gitlab-innersource-admin'
      ) {
        ansiColor('xterm') {
          // Webservice
          sh """
            docker tag \
              ${WS_LOCAL_IMAGE} \
              ${WS_DEPLOY_IMAGE}:${IMAGE_VERSION}
          """

          sh """
            docker push ${WS_DEPLOY_IMAGE}:${IMAGE_VERSION}
          """

          // Database
          sh """
            docker tag \
              ${DB_LOCAL_IMAGE} \
              ${DB_DEPLOY_IMAGE}:${IMAGE_VERSION}
          """

          sh """
            docker push ${DB_DEPLOY_IMAGE}:${IMAGE_VERSION}
          """
        }
      }
    }

    stage('Trigger Deploy') {
      build(
        job: 'deploy-ws',
        parameters: [
          string(name: 'IMAGE_VERSION', value: IMAGE_VERSION)
        ],
        propagate: false,
        wait: false
      )
    }
  } catch (e) {
    mail to: 'emartinez@usgs.gov',
      from: 'noreply@jenkins',
      subject: 'Jenkins: hazdev-design-ws',
      body: "Project build (${BUILD_TAG}) failed '${e}'"

    FAILURE = e
  } finally {
    stage('Cleanup') {
      sh """
        set +e

        # Cleaning up any leftover containers...
        docker container rm --force \
          ${OWASP_CONTAINER} \
          ${WS_PENTEST_CONTAINER}

        # Cleaning up any leftover images...
        docker image rm --force \
          ${WS_DEPLOY_IMAGE} \
          ${WS_LOCAL_IMAGE} \
          ${DB_DEPLOY_IMAGE} \
          ${DB_LOCAL_IMAGE}

        exit 0
      """

      if (FAILURE) {
        currentBuild.result = 'FAILURE'
        throw FAILURE
      }
    }
  }
}
