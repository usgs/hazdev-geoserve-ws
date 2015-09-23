/* global after, before, chai, describe, it  */
'use strict';

var TimezoneRegionView = require('geoserve/TimezoneRegionView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var expect,
    regions;

expect = chai.expect;


describe('TimezoneRegionView test suite.', function () {

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
      expect(TimezoneRegionView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('Can be instantiated', function () {
      var c = TimezoneRegionView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });
  });
  describe('Test View', function () {
    it('Shows header when header is passed', function () {
      var div,
          header,
          text;

      div = document.createElement('div');
      text = 'Test Header';
      header = '<h3 class="header">' + text + '</h3>';

      TimezoneRegionView({
        el: div,
        model: Model(),
        header: header
      });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });

    it('renders with and without data', function () {
      var noData,
          view;

      noData = 'No data';

      // No data
      view = TimezoneRegionView({
        header: null,
        model: Model(),
        noDataMessage: noData
      });
      expect(view.el.innerHTML).to.equal(noData);
      view.destroy();

      // Has data
      view = TimezoneRegionView({
        header: null,
        model: Model({
          regions: regions
        })
      });
      expect(view.el.innerHTML).to.equal([
        '<dl>',
          '<dt>Time Zone</dt>',
            '<dd>America/Denver</dd>',
          '<dt>Standard Offset</dt>',
            '<dd>-420</dd>',
          '<dt>DST Start</dt>',
            '<dd>2015-03-08T09:00:00Z</dd>',
          '<dt>DST End</dt>',
            '<dd>2015-11-01T08:00:00Z</dd>',
          '<dt>DST Offset</dt>',
            '<dd>-360</dd>',
        '</dl>'
      ].join(''));
      view.destroy();
    });

    it('Can destroy all the things', function () {
      var destroy = function () {
        var view = TimezoneRegionView();
        view.destroy();
      };
      expect(destroy).to.not.throw(Error);
    });
  });
});
