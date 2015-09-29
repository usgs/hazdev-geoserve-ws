'use strict';

var AuthoritativeRegionView = require('geoserve/AuthoritativeRegionView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');

var model;

model = Model({
  region: null
});

AuthoritativeRegionView({
  el: document.querySelector('#example-nodata'),
  header: '<h3>Authoritative Region (Without Data)</h3>',
  model: Model({
    regions: null
  })
});

AuthoritativeRegionView({
  el: document.querySelector('#example'),
  header: '<h3>Authoritative Region (With Data)</h3>',
  model: model
});

Xhr.ajax({
  url: 'regions.json',
  success: function (data) {
    model.set({
      region: data
    });
  }
});
