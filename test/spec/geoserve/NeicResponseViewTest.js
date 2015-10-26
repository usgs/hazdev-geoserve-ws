/* global after, before, chai, describe, it */
'use strict';

var NeicResponseView = require('geoserve/NeicResponseView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var expect,
    regions;

expect = chai.expect;


describe('NeicResponseView test suite.', function () {

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

    it('can be instantiated', function () {
      var c = new NeicResponseView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be created and destroyed', function () {
      var createDestroy;

      createDestroy = function () {
        var view = NeicResponseView();
        view.destroy();
      };

      expect(createDestroy).to.not.throw(Error);
    });
  });

  describe('Render', function () {
    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      NeicResponseView({
        el: div,
        header: null,
        model: Model({
          regions: regions
        })
      });

      expect(div.innerHTML).to.equal([
        '<dl class="horizontal">',
          '<dt>Name</dt>',
            '<dd>Western US</dd>',
          '<dt>Type</dt>',
            '<dd>US</dd>',
          '<dt>Magnitude</dt>',
            '<dd>4.0</dd>',
        '</dl>'
      ].join(''));
    });

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = '<p class="alert">hello</p>';

      NeicResponseView({
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

      NeicResponseView({
          el: div,
          header: header,
          model: Model()
        });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });
  });

});
