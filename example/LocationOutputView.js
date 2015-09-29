'use strict';

var LocationOutputView = require('geoserve/LocationOutputView'),

    Model = require('mvc/Model');

LocationOutputView({
  el: document.querySelector('#example-nodata'),
  header: '<h2>Example without data</h2>',
  model: Model({
    location: null
  })
});

LocationOutputView({
  el: document.querySelector('#example-noplace'),
  header: '<h2>Example with data (without placename)</h2>',
  model: Model({location: {
    latitude: 39.739,
    longitude: -104.985
  }})
});

LocationOutputView({
  el: document.querySelector('#example'),
  header: '<h2>Example with data (with placename)</h2>',
  model: Model({location: {
    latitude: 39.739,
    longitude: -104.985,
    place: 'Denver, CO'
  }})
});
