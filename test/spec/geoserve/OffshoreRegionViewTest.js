/* global after, before, chai, describe, it */
'use strict';

var Model = require('mvc/Model'),
    OffshoreRegionView = require('geoserve/OffshoreRegionView'),
    Xhr = require('util/Xhr');

var expect,
    regions;

expect = chai.expect;

describe('OffshoreRegionView test', function () {

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
      expect(OffshoreRegionView).not.to.be.null;
      expect(OffshoreRegionView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be instantiated', function () {
      var c = new OffshoreRegionView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be created and destroyed', function () {
      var createDestroy;

      createDestroy = function () {
        var view = OffshoreRegionView();
        view.destroy();
      };

      expect(createDestroy).to.not.throw(Error);
    });
  });

  describe('Render', function () {
    it('shows custom header when header is passed', function () {
      var div,
          header,
          text;

      div = document.createElement('div');
      text = 'Test Header';
      header = '<h3 class="header">' + text + '</h3>';

      OffshoreRegionView({
        el: div,
        header: header,
        model: Model()
      });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });

    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      OffshoreRegionView({
        el: div,
        header: null,
        model: Model({
          regions: regions
        })
      });

      expect(div.innerHTML).to.equal([
        '<dl class="horizontal">',
          '<dt>Name</dt>',
            '<dd>Off the coast of Oregon</dd>',
        '</dl>'
      ].join(''));
    });

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = '<p class="alert">hello</p>';

      OffshoreRegionView({
          el: div,
          header: null,
          model: Model(),
          noDataMessage: text
        });

      expect(div.innerHTML).to.be.equal(text);
    });

    it('Can destroy all the things', function () {
      var destroy = function () {
        var view = OffshoreRegionView();
        view.destroy();
      };
      expect(destroy).to.not.throw(Error);
    });
  });
});
