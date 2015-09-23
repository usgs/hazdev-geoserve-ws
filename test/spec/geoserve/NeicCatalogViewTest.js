/* global after, before, chai, describe, it*/
'use strict';

var expect = chai.expect,
    NeicCatalogView = require('geoserve/NeicCatalogView'),
    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');


describe('NeicCatalogView test suite.', function () {
  describe('Constructor', function () {
    it('Can be defined', function () {
      /* jshint -W030 */
      expect(NeicCatalogView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('Can be instantiated', function () {
      var c = NeicCatalogView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });
  });

  describe('destroy', function () {
    var regions;
    it('has such a method', function () {
      expect(NeicCatalogView()).to.respondTo('destroy');
    });

    before(function (done) {
      Xhr.ajax({
        url: 'regions.json',
        success: function (regions) {
          regions = regions;
          done();
        }
      });
    });

    after(function () {
      regions = null;
    });

    it('can be destroyed', function () {
      var neicCatalogView = NeicCatalogView({
        model: Model({regions:regions})
      });
      expect(neicCatalogView).to.not.equal(undefined);
      expect(neicCatalogView.el).to.not.equal(null);

      neicCatalogView.destroy();
      expect(neicCatalogView.el).to.equal(null);
    });

  });

});
