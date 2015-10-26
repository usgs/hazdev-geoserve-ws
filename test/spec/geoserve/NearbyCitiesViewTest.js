/* global after, before, chai, describe, it */
'use strict';

var NearbyCitiesView = require('geoserve/NearbyCitiesView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var expect,
    places;

expect = chai.expect;


describe('NearbyCitiesView test suite.', function () {

  before(function (done) {
    Xhr.ajax({
      url: 'places.json',
      success: function (data) {
        places = data;
        done();
      }
    });
  });

  after(function () {
    places = null;
  });


  describe('Constructor', function () {
    it('is defined', function () {
      /* jshint -W030 */
      expect(NearbyCitiesView).not.to.be.null;
      expect(NearbyCitiesView).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be instantiated', function () {
      var c = new NearbyCitiesView();
      /* jshint -W030 */
      expect(c).not.to.be.undefined;
      /* jshint +W030 */
    });

    it('can be created and destroyed', function () {
      var createDestroy;

      createDestroy = function () {
        var view = NearbyCitiesView();
        view.destroy();
      };

      expect(createDestroy).to.not.throw(Error);
    });
  });

  describe('Render', function () {
    it('shows data when data is available', function () {
      var div;

      div = document.createElement('div');

      NearbyCitiesView({
        el: div,
        model: Model({
          places: places
        })
      });

      expect(div.innerHTML).to.have.string([
        '<aside class="distance">9.2km (5.7mi)'
      ].join(''));
    });

    it('shows custom message when no data is available', function () {
      var div,
          text;

      div = document.createElement('div');
      text = '<p class="alert">hello</p>';

      NearbyCitiesView({
          el: div,
          model: Model(),
          noDataMessage: text
        });

      expect(div.innerHTML).to.be.equal(text);
    });
  });

});
