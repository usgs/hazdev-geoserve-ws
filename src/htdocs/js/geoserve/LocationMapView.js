/* global L */
'use strict';

var FullscreenControl = require('leaflet/FullscreenControl'),
    LocationControl = require('locationview/LocationControl'),
    MousePositionControl = require('leaflet/MousePositionControl'),
    Util = require('util/Util'),
    View = require('mvc/View');


var _DEFAULTS = {};


/**
 * TODO: leaflet map with location view control to show/set location.
 */
var LocationMapView = function (options) {
  var _this,
      _initialize,

      _locationControl,
      _map,

      _onLocationChange;


  _this = View(options);

  _initialize = function (options) {
    var el;

    options = Util.extend({}, _DEFAULTS, options);

    _this.el.classList.add('location-map-view');
    _this.el.innerHTML = '<div class="map"></div>';

    el = _this.el.querySelector('.map');
    _map = L.map(el, {
      scrollWheelZoom: false,
      zoomAnimation: false,
      attributionControl: false // This is added later, but order matters
    });
    _map.fitBounds([[24.6, -125.0], [50.0, -65.0]]);

    _map.addLayer(L.tileLayer('http://{s}.arcgisonline.com/ArcGIS/rest/services/' +
        'NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}.jpg', {
      subdomains: ['server', 'services'],
      attribution: 'Content may not reflect National Geographic\'s ' +
          'current map policy. Sources: National Geographic, Esri, ' +
          'DeLorme, HERE, UNEP-WCMC, USGS, NASA, ESA, METI, NRCAN, ' +
          'GEBCO, NOAA, increment P Corp.'
    }));

    // Add location control
    _locationControl = new LocationControl({
      el: el,
      includeGeolocationControl: true,
      includeGeocodeControl: true,
      includeCoordinateControl: true,
      includePointControl: true
    });
    _locationControl.on('location', _onLocationChange);
    _map.addControl(_locationControl);
    _locationControl.enable();

    // Add Map Controls
    if (!Util.isMobile()) {
      _map.addControl(L.control.attribution());
      _map.addControl(L.control.scale());
      _map.addControl(new FullscreenControl());
      _map.addControl(new MousePositionControl());
    }
  };

  /**
   * Update app location when location control changes location.
   */
  _onLocationChange = function () {
    var controlLocation,
        location;

    controlLocation = _locationControl.getLocation();
    location = _this.model.get('location');
    if (controlLocation !== location &&
        (
          !location ||
          location.latitude !== controlLocation.latitude ||
          location.longitude !== controlLocation.longitude
        )) {
      _this.model.set({
        location: controlLocation
      });
    }
  };

  /**
   * Update map to display current location.
   */
  _this.render = function () {
    var controlLocation,
        location;

    controlLocation = _locationControl.getLocation();
    location = _this.model.get('location');
    if (location) {
      if (controlLocation &&
          controlLocation !== location &&
          (
            location.latitude !== controlLocation.latitude ||
            location.longitude !== controlLocation.longitude
          )) {
        _locationControl.setLocation(location);
        _locationControl.disable();
      }
    } else {
      _locationControl.setLocation(null);
      _locationControl.enable();
    }
  };

  /**
   * View destroy method.
   */
  _this.destroy = Util.compose(function () {
    // remove event listeners
    _locationControl.off('location', _onLocationChange);
    _map.removeControl(_locationControl);

    // variables
    _locationControl = null;
    _map = null;

    // methods
    _onLocationChange = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);


  _initialize(options);
  options = null;
  return _this;
};


module.exports = LocationMapView;
