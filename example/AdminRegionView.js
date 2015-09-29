'use strict';

var AdminRegionView = require('geoserve/AdminRegionView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');

var model;

model = Model({
  regions: null
});

AdminRegionView({
  el: document.querySelector('#example-nodata'),
  header: '<h3>Administrative Region (Without Data)</h3>',
  model: Model({
    regions: null
  })
});

AdminRegionView({
  el: document.querySelector('#example'),
  header: '<h3>Administrative Region (With Data)</h3>',
  model: model
});

Xhr.ajax({
  url: 'regions.json',
  success: function (data) {
    model.set({
      regions: data
    });
  }
});
