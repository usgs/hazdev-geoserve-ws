/* global after, before, chai, describe, it */
'use strict';

var NearbyCitiesView = require('geoserve/NearbyCitiesView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var expect,
    places;

expect = chai.expect;


describe('NearbyCitiesView', function () {

  before(function (done) {
    Xhr.ajax({
      url: 'places.json',
      success: function (data) {
        places = data;
        done();
      }
    });
  });

  after(function () {
    places = null;
  });


  describe('constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(NearbyCitiesView).not.to.be.null;
      expect(NearbyCitiesView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be constructed without blowing up', function () {
      var construct;

      construct = function () {
        NearbyCitiesView();
      };

      expect(construct).not.to.throw(Error);
    });

    it('renders initially', function () {
      var noData,
          view;

      noData = 'No Data';

      // ... with no data ...
      view = NearbyCitiesView({
        noDataMessage: noData,
        model: Model()
      });
      expect(view.el.innerHTML).to.equal(noData);
      view.destroy();

      // ... with data ...
      view = NearbyCitiesView({
        model: Model({places: places})
      });
      expect(view.el.innerHTML).to.have.string([
        '<ol class="nearbyCities no-style">',
        '<li><span class="citydistance">9.156km (6mi)'
      ].join(''));
      view.destroy();
    });
  });
});
