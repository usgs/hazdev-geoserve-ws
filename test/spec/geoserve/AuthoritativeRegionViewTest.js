/* global chai, describe, it */
'use strict';

var AuthoritativeRegionView = require('geoserve/AuthoritativeRegionView'),
    Model = require('mvc/Model');

var expect = chai.expect;

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

  describe('View', function () {

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

      expect(div.querySelector('.alert').innerHTML).to.be.equal(text);
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