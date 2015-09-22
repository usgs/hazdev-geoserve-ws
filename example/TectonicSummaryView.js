'use string';

var TectonicSummaryView = require('geoserve/TectonicSummaryView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var _initialize,
    _model;

_initialize = function () {
  _model = Model({
    regions: null
  });

  TectonicSummaryView({
    header: '<h2>Example with data</h2>',
    el: document.querySelector('#example')
  });

  TectonicSummaryView({
    header: '<h2>Example without data</h2>',
    el: document.querySelector('#example-nodata')
  });

  Xhr.ajax({
    url: 'regions.json',
    success: _onRegions,
    error: _onRegionsError,
  });
};


_onRegions = function (regions) {
  _model.set({regions: regions});
};

_onRegionsError = function () {
  var p = document.body.appendChild(document.createElement('p'));

  p.className = 'alert error';
  p.innerHTML = 'Failed to load regions.json data.';
};

_initialize();
