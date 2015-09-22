'use strict';

var TimezoneRegionView = require('geoserve/TimezoneRegionView'),

    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');

var model;

model = Model({
  regions:null
});

TimezoneRegionView({
  el: document.querySelector('#example'),
  model: model,
  header: '<h3>Time Zone</h3>'
});

Xhr.ajax({
  url: 'regions.json',
  success:function (data) {
    model.set({
      regions: data
    });
  }
});
