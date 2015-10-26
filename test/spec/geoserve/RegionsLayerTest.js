/* global chai, describe, it */
'use strict';

var RegionsLayer = require('geoserve/RegionsLayer');


var expect;

expect = chai.expect;


describe('RegionsLayer test suite.', function () {

  describe('Constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(RegionsLayer).not.to.be.null;
      expect(RegionsLayer).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be instantiated', function () {
      var c = new RegionsLayer();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });
  });

});
