'use strict';

var AuthoritativeRegionView = require('geoserve/TimezoneRegionView'),

  Model = require('mvc/Model'),
  Xhr = require('util/Xhr');

Xhr.ajax({
  url: 'regions.json',
  success:function (data) {
    TimezoneRegionView({
      el: document.querySelector('#example'),
      data: Model({
        regions: data
      })
    });
  }
});
