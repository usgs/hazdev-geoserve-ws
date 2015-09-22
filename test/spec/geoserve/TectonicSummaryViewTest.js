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
      expect(view.el.innerHTML).to.equal(TectonicSummaryView.NO_DATA_MESSAGE);
      view.destroy();

      // ... with data ...
      view = TectonicSummaryView({
        header: null,
        model: Model({regions: regions})
      });
      expect(view.el.innerHTML).to.equal([
        '<dl>',
          '<dt>Name</dt>',
            '<dd>Eastern US</dd>',
          '<dt>Type</dt>',
            '<dd>US</dd>',
          '<dt>Magnitude</dt>',
            '<dd>3.0</dd>',
        '</dl>'
      ].join(''));
      view.destroy();
    });
  });

  describe('View', function () {

		it('shows a message when no data is available', function () {
			var div,
					text;

			div = document.createElement('div');
			text = 'No data to display.';

			TectonicSummaryView({
					el: div,
					model: Model()
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
