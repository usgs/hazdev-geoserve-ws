/* global after, before, chai, describe, it */
'use strict';

var TectonicSummaryView = require('geoserve/TectonicSummaryView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var expect,
    regions;

expect = chai.expect;


describe('TectonicSummaryView', function () {

  before(function (done) {
    Xhr.ajax({
      url: 'regions.json',
      success: function (data) {
        regions = data;
        done();
      }
    });
  });

  after(function () {
    regions = null;
  });


  describe('constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(TectonicSummaryView).not.to.be.null;
      expect(TectonicSummaryView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be constructed without blowing up', function () {
      var construct;

      construct = function () {
        TectonicSummaryView();
      };

      expect(construct).not.to.throw(Error);
    });

    it('renders initially', function () {
      var view;

      // ... with no data ...
      view = TectonicSummaryView({
        header: null,
        model: Model()
      });
      expect(view.el.innerHTML).to.equal(
        '<p class="alert info">' +
          'Tectonic summary data not available.' +
        '</p>');
      view.destroy();

      // ... with data ...
      view = TectonicSummaryView({
        header: null,
        model: Model({regions: regions})
      });
      expect(view.el.innerHTML).to.equal([
        '<div class="tectonic-summary">' +
          '<h4>Earthquakes in the Stable Continental Region</h4>\n\n' +
          '<h4>Natural Occurring Earthquake Activity</h4>\n\n' +
          '<p>\nMost of North America east of the Rocky Mountains has infrequent earthquakes. Here and there earthquakes are more numerous, for example in the New Madrid seismic zone centered on southeastern Missouri, in the Charlevoix-Kamouraska seismic zone of eastern Quebec, in New England, in the New York - Philadelphia - Wilmington urban corridor, and elsewhere. However, most of the enormous region from the Rockies to the Atlantic can go years without an earthquake large enough to be felt, and several U.S. states have never reported a damaging earthquake.\n</p>\n' +
          '<p>\nEarthquakes east of the Rocky Mountains, although less frequent than in the West, are typically felt over a much broader region than earthquakes of similar magnitude in the west. East of the Rockies, an earthquake can be felt over an area more than ten times larger than a similar magnitude earthquake on the west coast. It would not be unusual for a magnitude 4.0 earthquake in eastern or central North America to be felt by a significant percentage of the population in many communities more than 100 km (60 mi) from its source. A magnitude 5.5 earthquake in eastern or central North America might be felt by much of the population out to more than 500 km (300 mi) from its source. Earthquakes east of the Rockies that are centered in populated areas and large enough to cause damage are, similarly, likely to cause damage out to greater distances than earthquakes of the same magnitude centered in western North America.\n</p>\n' +
          '<p>\nMost earthquakes in North America east of the Rockies occur as faulting within bedrock, usually miles deep.   Few earthquakes east of the Rockies, however, have been definitely linked to mapped geologic faults, in contrast to the situation at plate boundaries such as California\'s San Andreas fault system, where scientists can commonly use  geologic evidence to identify a fault that has produced a large earthquake and that is likely  to produce large future earthquakes.  Scientists who study eastern and central North America earthquakes often work from the hypothesis that modern earthquakes occur as the result of slip on preexisting faults that were formed in earlier geologic eras and that have been reactivated under the current stress conditions. The bedrock of Eastern North America is, however, laced with faults that were active in earlier geologic eras, and few of these faults are known to have been active in the current geologic era.  In most areas east of the Rockies, the likelihood of future damaging earthquakes is currently estimated from the frequencies and sizes of instrumentally recorded earthquakes or earthquakes documented in historical records.\n</p>\n\n' +
          '<h4>Induced Seismicity</h4>\n' +
          '<p>\nAs is the case elsewhere in the world, there is evidence that some  central and eastern North America earthquakes have been triggered or caused  by human activities that have altered  the stress conditions in earth\'s crust sufficiently to induce faulting.   Activities that have induced felt earthquakes in some geologic environments have included impoundment of water behind dams, injection of fluid into the earth\'s crust, extraction of fluid or gas, and removal of rock in mining or quarrying operations.  In much of eastern and central North America, the number of earthquakes suspected of having been induced is much smaller than the number of natural earthquakes, but in some regions, such as the south-central states of the U.S., a significant majority of recent earthquakes are thought by many seismologists to have been human-induced.  Even within areas with many human-induced earthquakes, however, the activity that seems to induce seismicity at one location may be taking place at many other locations without inducing felt earthquakes.  In addition, regions with frequent induced earthquakes may also be subject to damaging earthquakes that would have occurred independently of human activity.  Making a strong scientific case for a causative link between a particular human activity and a particular sequence of earthquakes typically involves special studies devoted specifically to the question.  Such investigations usually address the process by which the suspected triggering activity might have significantly altered stresses in the bedrock at the earthquake source, and they commonly address the ways in which the characteristics of the suspected human-triggered earthquakes differ from the characteristics of natural earthquakes in the region.\n</p>\n<!--\n SCR.doc, 07/23/03, Page 1 of 1\n -->' +
        '</div>'
      ].join(''));
      view.destroy();
    });
  });

  describe('View', function () {

		it('shows a custom message when no data is available', function () {
			var div,
					text;

			div = document.createElement('div');
			text = 'No data to display.';

			TectonicSummaryView({
					el: div,
					model: Model(),
          noDataMessage: text
				});

			expect(div.querySelector('.alert').innerHTML).to.be.equal(text);
		});


		it('shows custom header when header is passed', function () {
			var div,
					header,
					text;

			div = document.createElement('div');
			text = 'Header';
			header = '<h2 class="tectonic-summary-header">' + text + '</h2>';

			TectonicSummaryView({
          header: header,
					el: div,
					model: Model()
				});

			expect(div.querySelector('.tectonic-summary-header').innerHTML).to.be.equal(text);
		});

	});
});
