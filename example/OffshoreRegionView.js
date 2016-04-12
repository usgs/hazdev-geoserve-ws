'use strict';

var OffshoreRegionView = require('geoserve/OffshoreRegionView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');

var model;

model = Model({
  regions: null
});

OffshoreRegionView({
  el: document.querySelector('#example-nodata'),
  header: '<h2>Example without data</h2>',
  model: Model({
    regions: null
  })
});

OffshoreRegionView({
  el: document.querySelector('#example'),
  header: '<h2>Example with data</h2>',
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
