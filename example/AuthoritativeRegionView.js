'use strict';

var AuthoritativeRegionView = require('geoserve/AuthoritativeRegionView'),

    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');

var model;

model = Model({
  region: null
});

AuthoritativeRegionView({
  el: document.querySelector('#example1'),
  model: model,
  header: '<h3>Authoritative Region (With Data)</h3>'
});

AuthoritativeRegionView({
  el: document.querySelector('#example2'),
  model: Model({
    regions: null
  }),
  header: '<h3>Authoritative Region (Without Data)</h3>'
});

Xhr.ajax({
  url: 'regions.json',
  success: function (data) {
    model.set({
      region: data
    });
  }
});
