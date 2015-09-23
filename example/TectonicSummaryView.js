'use string';

var TectonicSummaryView = require('geoserve/TectonicSummaryView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');


var _initialize,
    _model,
    _onRegions,
    _onRegionsError;

_initialize = function () {
  _model = Model({
    regions: null
  });

  TectonicSummaryView({
    el: document.querySelector('#example-nodata'),
    header: '<h2>Example without data</h2>',
    model: Model({
      regions: null
    })
  });

  TectonicSummaryView({
    el: document.querySelector('#example'),
    header: '<h2>Example with data</h2>',
    model: _model
  });

  Xhr.ajax({
    error: _onRegionsError,
    success: _onRegions,
    url: 'regions.json'
  });
};


_onRegions = function (regions) {
  _model.set({
    regions: regions
  });
};

_onRegionsError = function () {
  var p = document.body.appendChild(document.createElement('p'));

  p.className = 'alert error';
  p.innerHTML = 'Failed to load regions.json data.';
};

_initialize();
