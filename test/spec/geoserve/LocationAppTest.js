/* global chai, sinon, describe, it, before, after */
'use strict';

var LocationApp = require('geoserve/LocationApp'),

    Xhr = require('util/Xhr');

var expect;

expect = chai.expect;


describe('a LocationApp test suite.', function () {
  describe('Constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(LocationApp).not.to.be.null;
      expect(LocationApp).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be instantiated', function () {
      var c = new LocationApp();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be created and destroyed', function () {
      var createDestroy;

      createDestroy = function () {
        var view = LocationApp();
        view.destroy();
      };

      expect(createDestroy).to.not.throw(Error);
    });
  });

  // just testing inheritance from EventModule
  describe('Destroy', function () {
    it('has such a method', function () {
      expect(LocationApp()).to.respondTo('destroy');
    });
  });

  describe('Responds to location change', function () {
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
