'use strict';

var TimezoneRegionView = require('geoserve/TimezoneRegionView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');

var model;

model = Model({
  regions: null
});

TimezoneRegionView({
  el: document.querySelector('#example-nodata'),
  header: '<h3>Time Zone No Data</h3>'
});

TimezoneRegionView({
  el: document.querySelector('#example'),
  header: '<h3>Time Zone</h3>',
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
