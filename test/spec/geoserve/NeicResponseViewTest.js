/* global after, before, chai, describe, it */
'use strict';

var NeicResponseView = require('geoserve/NeicResponseView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var expect,
    regions;

expect = chai.expect;


describe('NeicResponseView', function () {

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


  describe('constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(NeicResponseView).not.to.be.null;
      expect(NeicResponseView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be constructed without blowing up', function () {
      var construct;

      construct = function () {
        NeicResponseView();
      };

      expect(construct).not.to.throw(Error);
    });

    it('renders initially', function () {
      var noData,
          view;

      noData = 'No data';

      // ... with no data ...
      view = NeicResponseView({
        header: null,
        model: Model(),
        noDataMessage: noData
      });
      expect(view.el.innerHTML).to.equal(noData);
      view.destroy();

      // ... with data ...
      view = NeicResponseView({header: null, model: Model({regions: regions})});
      expect(view.el.innerHTML).to.equal([
        '<dl>',
          '<dt>Name</dt>',
            '<dd>Eastern US</dd>',
          '<dt>Type</dt>',
            '<dd>US</dd>',
          '<dt>Magnitude</dt>',
            '<dd>3.0</dd>',
        '</dl>'
      ].join(''));
      view.destroy();
    });
  });
});
