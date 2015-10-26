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
    it('is defined', function () {
      /* jshint -W030 */
      expect(NeicCatalogView).not.to.be.null;
      expect(NeicCatalogView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be instantiated', function () {
      var c = new NeicCatalogView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be created and destroyed', function () {
      var createDestroy;

      createDestroy = function () {
        var view = NeicCatalogView();
        view.destroy();
      };

      expect(createDestroy).to.not.throw(Error);
    });
  });

  describe('Destroy', function () {
    it('has such a method', function () {
      expect(NeicCatalogView()).to.respondTo('destroy');
    });

    it('can be destroyed', function () {
      var neicCatalogView = NeicCatalogView({
        model: Model({
          regions: regions
        })
      });

      expect(neicCatalogView).to.not.equal(undefined);
      expect(neicCatalogView.el).to.not.equal(null);

      neicCatalogView.destroy();
      expect(neicCatalogView.el).to.equal(null);
    });
  });

  describe('Render', function () {
    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      NeicCatalogView({
        el: div,
        header: null,
        model: Model({
          regions: regions
        })
      });

      expect(div.innerHTML).to.equal([
        '<dl class="horizontal">',
          '<dt>Name</dt>',
            '<dd>California</dd>',
          '<dt>Type</dt>',
            '<dd>US</dd>',
          '<dt>Magnitude</dt>',
            '<dd>3.0</dd>',
              '<dl></dl>',
        '</dl>'
      ].join(''));
    });

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = '<p class="alert">hello</p>';

      NeicCatalogView({
          el: div,
          header: null,
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
      header = '<h3 class="header">' + text + '</h3>';

      NeicCatalogView({
          el: div,
          header: header,
          model: Model()
        });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });
  });

});
