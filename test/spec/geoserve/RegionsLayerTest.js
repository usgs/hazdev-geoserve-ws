/* global chai, describe, it */
'use strict';

var expect = chai.expect,
    RegionsLayer = require('geoserve/RegionsLayer');


describe('RegionsLayer test suite.', function () {
  describe('Constructor', function () {
    it('Can be defined', function () {
      /* jshint -W030 */
      expect(RegionsLayer).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('Can be instantiated', function () {
      var c = new RegionsLayer();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });
  });
});
