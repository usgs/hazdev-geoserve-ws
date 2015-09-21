'use strict';

var AuthoritativeRegionView = require('geoserve/AuthoritativeRegionView'),

    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');

Xhr.ajax({
  url: 'data.json',
  success: function (data) {
    AuthoritativeRegionView({
      el: document.querySelector('#example'),
      data: Model(data.authoritative.features[0].properties)
    });
  }
});
