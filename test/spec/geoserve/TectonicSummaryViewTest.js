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
          'Tectonic summary not available.' +
        '</p>');
      view.destroy();

      // ... with data ...
      view = TectonicSummaryView({
        header: null,
        model: Model({regions: regions})
      });
      expect(view.el.innerHTML).to.equal([
        '<h4>San Andreas Fault</h4>\n',
        '<p>\nThe San Andreas Fault forms the main strand of the plate ',
        'boundary, running\n \nfrom the Gulf of California (Baja ',
        'California, Mexico) north to the region\n \nof Cape Mendocino.  ',
        'The fault in the San Francisco Bay region is a largely\n \n',
        'strike-slip fault running through the Santa Cruz Mountains, the ',
        'Gulf of the\n \nFarallons west of the Golden Gate, through Tomales ',
        'Bay and Bodega Bay, and\n \nnorth to Fort Ross and Point Arena.  ',
        'Northward of Point Arena, the location\n \nand character of the San ',
        'Andreas Fault is less well known.   The fault in\n \nthis region ',
        'is locked, exhibiting no creep at the surface and generating\n ',
        '\nvery few microearthquakes that are associated with minor slipping ',
        'at depth.\n \nThrough the San Francisco Bay Area, the slip rate on ',
        'the San Andreas Fault\n \nis about 20 mm/yr (4/5 inch/year).\n \n</p>',
        '\n<p>\nThe October 17, 1989 Loma Prieta earthquake was the most ',
        'recent major\n \nearthquake associated with the San Andreas Fault. ',
        'While the earthquake was\n \nnot produced by the main San Andreas ',
        'Fault, it occurred on a closely\n \nassociated blind thrust fault ',
        'that had formed as a result of a bend in the\n \nSan Andreas Fault, ',
        'south of the bay.  Although that earthquake struck along\n \na ',
        'remote segment of the Santa Cruz Mountains, 64 deaths resulted, ',
        'most from\n \nthe collapse of the Cypress Freeway in Oakland.  ',
        'About 16,000 homes and\n \napartment units were uninhabitable after ',
        'the earthquake.  The San\n \nFrancisco-Oakland Bay Bridge was closed ',
        'for more than a month because of a\n \ncollapse of a section of its ',
        'eastern span.\n \n</p>\n<p>\nThe left bend in the San Andreas Fault ',
        'in the Santa Cruz Mountains favors\n \nthickening of the crust and ',
        'uplift of the Earth\'s surface, and is thought\n \nto be responsible ',
        'to the formation of the Santa Cruz Mountains.\n \n</p>\n<p>\nThe ',
        'M7.9 April 18, 1906 San Francisco earthquake was the most recent ',
        'great\n \nearthquake on the San Andreas Fault and it ruptured from ',
        'approximately Cape\n \nMendocino south to San Juan Bautista.  The ',
        '1906 earthquake was the largest\n \nearthquake to strike Northern ',
        'California in historic times, and is thought\n \nto have killed more ',
        'than 3,000 Bay Area residents.  The epicenter of that\n \nearthquake ',
        'is now estimated to be offshore about 2 miles west of San\n \n',
        'Francisco. The fire following the 1906 earthquake burned 5 square ',
        'miles of\n \nSan Francisco and resulted in 225,000 homeless refugees ',
        'of the earthquake.\n \n</p>\n<p>\nA large (magnitude 6.8) earthquake ',
        'in 1838 is often assumed to have\n \noccurred on the Peninsula ',
        'segment of the San Andreas Fault.  To date,\n \nhowever, ',
        'unambiguous observations placing that earthquake on the San\n \n',
        'Andreas Fault have not been found.\n \n</p>\n<p>\nThe 2003 Working ',
        'Group for California Earthquake Probability assigned a 21%\n \n',
        'probability that the San Andreas Fault would produce a magnitude ',
        '6.7 or\n \nlarger earthquake in the next 30 years.\n \n</p>'
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

			expect(div.innerHTML).to.be.equal(text);
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
