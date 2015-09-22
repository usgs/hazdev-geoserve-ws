/* global chai, describe, it  */
'use strict';

var expect = chai.expect,
    TimezoneRegionView = require('geoserve/TimezoneRegionView'),
    Model = require('mvc/Model');


describe('TimezoneRegionView test suite.', function () {
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

    it('Can destroy all the things', function () {
      var destroy = function () {
        var view = TimezoneRegionView();
        view.destroy();
      }
      expect(destroy).to.not.throw('Error');
    });
  });
});
