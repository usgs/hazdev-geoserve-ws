/* global chai, describe, it */
'use strict';

var AdminRegionView = require('geoserve/AdminRegionView'),
    Model = require('mvc/Model');

var expect = chai.expect;

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

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = 'hello';

      AdminRegionView({
          el: div,
          model: Model(),
          noDataMessage: text
        });

      expect(div.querySelector('.alert').innerHTML).to.be.equal(text);
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