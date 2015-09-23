'use strict';

var NearbyCityView = require('geoserve/NearbyCitiesView'),

    Model = require('mvc/Model'),

    Xhr = require('util/Xhr');

var _initialize,
    _model,
    _onPlaces,
    _onPlacesError;

_initialize = function () {
  _model = Model({places: null});

  NearbyCityView({
    header: '<h2>Example with data</h2>',
    el: document.querySelector('#example'),
    model: _model
  });

  NearbyCityView({
    header: '<h2>Example without data</h2>',
    el: document.querySelector('#example-nodata'),
    model: Model({places: null})
  });

  Xhr.ajax({
    url: 'places.json',
    success: _onPlaces,
    error: _onPlacesError,
  });
};


_onPlaces = function (places) {
  _model.set({places: places});
};

_onPlacesError = function () {
  var p = document.body.appendChild(document.createElement('p'));

  p.className = 'alert error';
  p.innerHTML = 'Failed to load places.json data.';
};

_initialize();
