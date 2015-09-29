'use strict';

var NearbyCityView = require('geoserve/NearbyCitiesView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');

var model;

model = Model({
  places: null
});

// Instantiate with empty model.
NearbyCityView({
  el: document.querySelector('#example-nodata'),
  header: '<h2>Example without data</h2>',
  model: Model({
    places: null
  })
});

// Instantiate with local model.
NearbyCityView({
  el: document.querySelector('#example'),
  header: '<h2>Example with data</h2>',
  model: model
});

Xhr.ajax({
  url: 'places.json',
  success: function(data) {
    model.set({
      places: data
    });
  }
});
