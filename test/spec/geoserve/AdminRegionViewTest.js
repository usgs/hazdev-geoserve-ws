/* global before, chai, describe, it */
'use strict';

var AdminRegionView = require('geoserve/AdminRegionView'),
    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');

var expect = chai.expect,
    regions;

describe('AdminRegionView test suite.', function () {
  describe('Constructor', function () {
    it('can be created and destroyed', function () {
      var createDestroy = function () {
        var view = AdminRegionView();
        view.destroy();
      };

      expect(createDestroy).to.not.throw(Error);
    });
  });

  describe('Render', function () {

    before(function (done) {
      Xhr.ajax({
        url: 'regions.json',
        success: function (data) {
          regions = data;
          done();
        }
      });
    });

    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      AdminRegionView({
        el: div,
        model: Model({regions: regions})
      });

      expect(div.innerHTML).to.be.equal('<dl class="horizontal">' +
          '<dt>ISO</dt><dd>USA</dd>' +
          '<dt>Country</dt><dd>United States</dd>' +
          '<dt>Region</dt><dd>California</dd>' +
        '</dl>');
    });

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = '<p class="alert">hello</p>';

      AdminRegionView({
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

      AdminRegionView({
          el: div,
          model: Model(),
          header: header
        });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });


  });
});
