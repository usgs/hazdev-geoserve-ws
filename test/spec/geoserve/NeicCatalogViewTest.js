/* global after, before, chai, describe, it*/
'use strict';

var NeicCatalogView = require('geoserve/NeicCatalogView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');

var expect,
    regions;

expect = chai.expect;

describe('NeicCatalogView test suite.', function () {

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
    it('has such a method', function () {
      expect(NeicCatalogView()).to.respondTo('destroy');
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

  describe('Test View', function () {
    it('Renders with and without data', function () {
      var noData,
          view;

      noData = 'Neic catalog not available.';

      // No data
      view = NeicCatalogView({
        header: null,
        model: Model(),
        noDataMessage: noData
      });
      expect(view.el.innerHTML).to.equal(noData);
      view.destroy();

      // Has data
      view = NeicCatalogView({
        header: null,
        model: Model({
          regions: regions
        })
      });
      expect(view.el.innerHTML).to.equal([
        '<dl class="horizontal">',
          '<dt>Name</dt>',
            '<dd>Contiguous US</dd>',
          '<dt>Type</dt>',
            '<dd>US</dd>',
          '<dt>Magnitude</dt>',
            '<dd>2.5</dd>',
              '<dl></dl>',
        '</dl>'
      ].join(''));
      view.destroy();
    });
  });
});
