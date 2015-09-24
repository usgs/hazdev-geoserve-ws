/* global chai, describe, it */
'use strict';

var LocationOutputView = require('geoserve/LocationOutputView'),

    Model = require('mvc/Model');


var expect;

expect = chai.expect;


describe('LocationOutputView', function () {



  describe('constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(LocationOutputView).not.to.be.null;
      expect(LocationOutputView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be constructed without blowing up', function () {
      var construct;

      construct = function () {
        LocationOutputView();
      };

      expect(construct).not.to.throw(Error);
    });

    it('renders initially', function () {
      var noData,
          view;

      noData = 'Use the map to select a location.';

      // ... with no data ...
      view = LocationOutputView({
        header: null,
        model: Model(),
        noDataMessage: noData
      });
      expect(view.el.innerHTML).to.equal(
        '<p class="alert info">' +
          noData +
        '</p>'
      );
      view.destroy();

      // ... with data ...
      view = LocationOutputView({header: null, model: Model({location: {
        place: 'Denver, CO',
        latitude: 39.739,
        longitude: -104.985
      }})});
      expect(view.el.innerHTML).to.equal([
        '<p class="alert success">',
          '<strong>Denver, CO</strong>',
          '<small>39.739°N, 104.985°W</small>',
        '</p>'
      ].join(''));
      view.destroy();
    });
  });
});
