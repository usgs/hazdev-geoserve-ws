/* global chai, describe, it */
'use strict';

var LocationOutputView = require('geoserve/LocationOutputView'),

    Model = require('mvc/Model');


var expect;

expect = chai.expect;


describe('LocationOutputView test suite.', function () {

  describe('Constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(LocationOutputView).not.to.be.null;
      expect(LocationOutputView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be instantiated', function () {
      var c = new LocationOutputView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be created and destroyed', function () {
      var createDestroy;

      createDestroy = function () {
        var view = LocationOutputView();
        view.destroy();
      };

      expect(createDestroy).to.not.throw(Error);
    });
  });

  describe('Render', function () {
    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      LocationOutputView({
        el: div,
        header: null,
        model: Model({
          location: {
            latitude: 39.739,
            longitude: -104.985,
            place: 'Denver, CO'
          }
        })
      });

      expect(div.innerHTML).to.equal([
        '<p class="alert success">',
          '<strong>Denver, CO</strong>',
          '<small>39.739°N, 104.985°W</small>',
        '</p>'
      ].join(''));
    });

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = '<p class="alert info">Use the map to select a location.</p>';

      LocationOutputView({
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

      LocationOutputView({
          el: div,
          header: header,
          model: Model()
        });

      expect(div.querySelector('.header').innerHTML).to.be.equal(text);
    });
  });

});
