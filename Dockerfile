ARG BUILD_IMAGE=usgs/hazdev-base-images:latest-node
ARG FROM_IMAGE=usgs/hazdev-base-images:latest-php


FROM ${BUILD_IMAGE} as buildenv


# php required for pre-install
RUN yum install -y \
		bzip2 \
		php

COPY package.json /hazdev-geoserve-ws/package.json
WORKDIR /hazdev-geoserve-ws

# install node dependencies
RUN /bin/bash --login -c "\
		npm install -g grunt-cli && \
		npm install \
		"

COPY . /hazdev-geoserve-ws

RUN /bin/bash --login -c "\
		php src/lib/pre-install.php --non-interactive && \
		grunt builddist \
		"



FROM ${FROM_IMAGE}

RUN yum install -y php-pgsql

COPY --from=buildenv /hazdev-geoserve-ws/node_modules/hazdev-template/dist/ /var/www/apps/hazdev-template/
COPY --from=buildenv /hazdev-geoserve-ws/dist/ /var/www/apps/hazdev-geoserve-ws/
COPY --from=buildenv /hazdev-geoserve-ws/src/lib/docker_template_config.php /var/www/html/_config.inc.php

# TODO: configure template in place...
# php /var/www/apps/hazdev-template/lib/pre-install.php --non-interactive && \
# ln -s /var/www/apps/hazdev-template/conf/httpd.conf /etc/httpd/conf.d/hazdev-template.conf && \
COPY --from=buildenv /hazdev-geoserve-ws/src/lib/docker_template_httpd.conf /etc/httpd/conf.d/hazdev-template.conf

		
# configure and install app
RUN /bin/bash --login -c "\
		php /var/www/apps/hazdev-geoserve-ws/lib/pre-install.php --non-interactive && \
		ln -s /var/www/apps/hazdev-geoserve-ws/conf/httpd.conf /etc/httpd/conf.d/hazdev-geoserve-ws.conf \
		"
