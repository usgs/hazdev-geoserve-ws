'use strict';

var NeicCatalogView = require('geoserve/NeicCatalogView'),
    Model = require('mvc/Model'),
    Xhr = require('util/Xhr'),
    tmp;

Xhr.ajax({
  url: 'neiccatalog.json',
  success: function (data) {
    tmp = 'hello';
    NeicCatalogView({
      el: document.querySelector('#example'),
      data: Model(data.neiccatalog.features[0].properties)
    });
  }
});
