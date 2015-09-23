'use strict';


var Util = require('util/Util'),
    View = require('mvc/View'),
    Xhr = require('util/Xhr'),

    LocationMapView = require('geoserve/LocationMapView'),
    LocationOutputView = require('geoserve/LocationOutputView'),
    NeicResponseView = require('geoserve/NeicResponseView');


var _DEFAULTS = {
  url: '/ws/geoserve'
};


/**
 * LocationApp is the controller for the "location" application.
 *
 * It creates a shared model, and configures that in all contained views.
 * When a location is set by one of the contained views, it requests
 * places and regions information from geoserve and updates the corresponding
 * model keys.
 *
 * @param options {Object}
 *        all options are passed to View().
 * @param options.url {String}
 *        default '/ws/geoserve'
 *        base url for geoserve web service.
 */
var LocationApp = function (options) {
  var _this,
      _initialize,

      _mapView,
      _neicResponseView,
      _outputView,
      _url,

      _onLocationChange;


  _this = View(options);

  _initialize = function (options) {
    var el;

    options = Util.extend({}, _DEFAULTS, options);
    _url = options.url;

    el = _this.el;
    el.innerHTML = '<section class="location-map-view"></section>' +
        '<section class="location-output-view"></section>' +
        '<section class="neicresponse-view"></section>';

    _this.model.on('change:location', _onLocationChange);

    _mapView = LocationMapView({
      el: el.querySelector('.location-map-view'),
      model: _this.model
    });

    _outputView = LocationOutputView({
      el: el.querySelector('.location-output-view'),
      model: _this.model
    });

    _neicResponseView = NeicResponseView({
      el: el.querySelector('.neicresponse-view'),
      model: _this.model
    });
  };

  /**
   * When new location is selected, clear existing information and request
   * new information.
   */
  _onLocationChange = function () {
    var location;

    // clear existing place/region infn
    _this.model.set({
      places: null,
      regions: null
    });

    location = _this.model.get('location');
    if (location !== null) {
      // request nearby
      Xhr.ajax({
        url: _url + '/places.json',
        data: {
          latitude: location.latitude,
          longitude: location.longitude,
          type: 'event'
        },
        success: function (data) {
          _this.model.set({
            places: data
          });
        }
      });

      // request region information
      Xhr.ajax({
        url: _url + '/regions.json',
        data: {
          latitude: location.latitude,
          longitude: location.longitude
        },
        success: function (data) {
          _this.model.set({
            regions: data
          });
        }
      });
    }
  };

  /**
   * View destroy method.
   */
  _this.destroy = Util.compose(function () {
    if (_this === null) {
      return;
    }

    // unbind event listeners
    _this.model.off('change:location', _onLocationChange);

    // destroy child views
    _mapView.destroy();
    _neicResponseView.destroy();
    _outputView.destroy();

    // free references
    _mapView = null;
    _neicResponseView = null;
    _outputView = null;
    _url = null;

    _onLocationChange = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);


  _initialize(options);
  options = null;
  return _this;
};


module.exports = LocationApp;
