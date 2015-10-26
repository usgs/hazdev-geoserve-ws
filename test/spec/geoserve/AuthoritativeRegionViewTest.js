/* global after, before, chai, describe, it */
'use strict';

var AuthoritativeRegionView = require('geoserve/AuthoritativeRegionView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var expect,
    region;

expect = chai.expect;


describe('AuthoritativeRegionView test suite.', function () {

  before(function (done) {
    Xhr.ajax({
      url: 'regions.json',
      success: function (data) {
        region = data;
        done();
      }
    });
  });

  after(function () {
    region = null;
  });


  describe('Constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(AuthoritativeRegionView).not.to.be.null;
      expect(AuthoritativeRegionView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be instantiated', function () {
      var c = new AuthoritativeRegionView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be created and destroyed', function () {
      var createDestroy;

      createDestroy = function () {
        var view = AuthoritativeRegionView();
        view.destroy();
      };

      expect(createDestroy).to.not.throw(Error);
    });
  });

  describe('Render', function () {
    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      AuthoritativeRegionView({
        el: div,
        model: Model({
          regions: region
        })
      });

      expect(div.innerHTML).to.be.equal(
        '<dl class="horizontal">' +
          '<dt>Name</dt>' +
            '<dd>NC</dd>' +
          '<dt>Network</dt>' +
            '<dd>NC</dd>' +
          '<dt>Type</dt>' +
            '<dd>A</dd>' +
        '</dl>'
      );
    });

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = 'hello';

      AuthoritativeRegionView({
          el: div,
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

      AuthoritativeRegionView({
          el: div,
          header: header,
          model: Model()
        });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });
  });

});
