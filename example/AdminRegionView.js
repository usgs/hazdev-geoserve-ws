'use strict';

var AdminRegionView = require('geoserve/AdminRegionView'),

    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');

var model;

model = Model({regions: null});

AdminRegionView({
  el: document.querySelector('#example1'),
  model: model,
  header: '<h3>Administrative Region (With Data)</h3>'
});

AdminRegionView({
  el: document.querySelector('#example2'),
  model: Model({
    regions: null
  }),
  header: '<h3>Administrative Region (Without Data)</h3>'
});


Xhr.ajax({
  url: 'regions.json',
  success: function (data) {
    model.set({
      regions: data
    });
  }
});