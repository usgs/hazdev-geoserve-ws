'use strict';

var AuthoritativeRegionView = require('geoserve/TimezoneRegionView'),

  Model = require('mvc/Model'),
  Xhr = require('util/Xhr');

Xhr.ajax({
  url: 'data.json',
  success:function (data) {
    TimezoneRegionView({
      el: document.querySelector('#example'),
      data: Model(data.timezone.features[0])
    });
  }
});
