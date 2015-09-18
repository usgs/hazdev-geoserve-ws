/* global chai, sinon, describe, it, before, after */
'use strict';

var expect = chai.expect,
    LocationApp = require('geoserve/LocationApp'),
    Xhr = require('util/Xhr');


describe('LocationApp test suite.', function () {
  describe('Constructor', function () {
    it('Can be defined', function () {
      /* jshint -W030 */
      expect(LocationApp).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('Can be instantiated', function () {
      var c = LocationApp();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });
  });

  // just testing inheritance from EventModule
  describe('destroy', function () {
    it('has such a method', function () {
      expect(LocationApp()).to.respondTo('destroy');
    });
  });

  describe('responds to location change', function () {
    var app,
        stub;

    before(function () {
      app = LocationApp();
      stub = sinon.stub(Xhr, 'ajax', function () {});
    });

    after(function () {
      stub.restore();
      app.destroy();
    });


    it('requests places and regions information', function () {
      app.model.set({
        location: {
          latitude: 34,
          longitude: -118
        }
      });

      expect(stub.getCall(0).args[0].url.indexOf('/places')).to.not.equal(-1);
      expect(stub.getCall(1).args[0].url.indexOf('/regions')).to.not.equal(-1);
    });
  });

});
