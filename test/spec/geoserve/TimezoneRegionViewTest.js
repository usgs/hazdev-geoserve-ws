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
    it('is defined', function () {
      /* jshint -W030 */
      expect(TimezoneRegionView).not.to.be.null;
      expect(TimezoneRegionView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be instantiated', function () {
      var c = new TimezoneRegionView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be created and destroyed', function () {
      var createDestroy;

      createDestroy = function () {
        var view = TimezoneRegionView();
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

      TimezoneRegionView({
        el: div,
        header: header,
        model: Model()
      });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });

    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      TimezoneRegionView({
        el: div,
        header: null,
        model: Model({
          regions: regions
        })
      });

      expect(div.innerHTML).to.equal([
        '<dl class="horizontal">',
          '<dt>Time Zone</dt>',
            '<dd>America/Los_Angeles</dd>',
          '<dt>Standard Offset</dt>',
            '<dd>-480</dd>',
          '<dt>DST Start</dt>',
            '<dd>2015-03-08T10:00:00Z</dd>',
          '<dt>DST End</dt>',
            '<dd>2015-11-01T09:00:00Z</dd>',
          '<dt>DST Offset</dt>',
            '<dd>-420</dd>',
        '</dl>'
      ].join(''));
    });

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = '<p class="alert">hello</p>';

      TimezoneRegionView({
          el: div,
          header: null,
          model: Model(),
          noDataMessage: text
        });

      expect(div.innerHTML).to.be.equal(text);
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
