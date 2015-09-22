'use strict';

var NeicCatalogView = require('geoserve/NeicCatalogView'),
    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');

Xhr.ajax({
  url: 'regions.json',
  success: function (regions) {
    NeicCatalogView({
      el: document.querySelector('#example'),
      model: Model({regions:regions}),
    });
  },
  error: function () {
    var p = document.body.appendChild(document.createElement('p'));
    p.className = 'alert error';
    p.innerHTML = 'Failed to load regions.json data.';
  }
});
