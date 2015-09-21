'use strict';

var AdminRegionView = require('geoserve/AdminRegionView'),

    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');

Xhr.ajax({
  url: 'data.json',
  success: function (data) {
    AdminRegionView({
      el: document.querySelector('#example'),
      data: Model(data.admin.features[0].properties)
    });
  }
});