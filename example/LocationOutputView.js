'use strict';

var LocationOutputView = require('geoserve/LocationOutputView'),

    Model = require('mvc/Model');

var _initialize;

_initialize = function () {
  LocationOutputView({
    header: '<h2>Example with data (with placename)</h2>',
    el: document.querySelector('#example'),
    model: Model({location: {
      place: 'Denver, CO',
      latitude: 39.739,
      longitude: -104.985
    }})
  });

  LocationOutputView({
    header: '<h2>Example with data (without placename)</h2>',
    el: document.querySelector('#example-noplace'),
    model: Model({location: {
      latitude: 39.739,
      longitude: -104.985
    }})
  });

  LocationOutputView({
    header: '<h2>Example without data</h2>',
    el: document.querySelector('#example-nodata'),
    model: Model({location: null})
  });
};

_initialize();
