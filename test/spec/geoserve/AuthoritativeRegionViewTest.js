/* global before, chai, describe, it */
'use strict';

var AuthoritativeRegionView = require('geoserve/AuthoritativeRegionView'),
    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');

var expect = chai.expect,
    region;

describe('AuthoritativeRegionView test suite.', function () {
  describe('Constructor', function () {
    it('can be created and destroyed', function () {
      var createDestroy = function () {
        var view = AuthoritativeRegionView();
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
          region = data;
          done();
        }
      });
    });

    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      AuthoritativeRegionView({
        el: div,
        model: Model({regions: region})
      });

      expect(div.innerHTML).to.be.equal('<dl class="horizontal">' +
          '<dt>Name</dt><dd>PAS</dd>' +
          '<dt>Network</dt><dd>CI</dd>' +
        '</dl>');
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
          model: Model(),
          header: header
        });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });


  });
});
