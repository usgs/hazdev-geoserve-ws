'use strict';

var RegionsLayer = require('geoserve/RegionsLayer'),
    L = require('leaflet'),
    LocationControl = require('locationview/LocationControl'),
    Util = require('util/Util'),
    View = require('mvc/View'),
    Xhr = require('util/Xhr');

require('leaflet/control/Fullscreen');
require('leaflet/control/MousePosition');
require('leaflet/layer/EsriGrayscale');
require('leaflet/layer/EsriTerrain');
require('leaflet/layer/OpenAerialMap');
require('leaflet/layer/OpenStreetMap');


var _DEFAULTS = {
  url: '/ws/geoserve'
};


/**
 * Leaflet map with location view control to show/set location.
 */
var LocationMapView = function (options) {
  var _this,
      _initialize,

      _layersControl,
      _locationControl,
      _map,
      _url,

      _loadOverlays,
      _onLocationChange;


  _this = View(options);

  _initialize = function (options) {
    var el;

    options = Util.extend({}, _DEFAULTS, options);
    _url = options.url;

    _this.el.classList.add('location-map-view');
    _this.el.innerHTML = '<div class="map"></div>';

    el = _this.el.querySelector('.map');
    _map = L.map(el, {
      scrollWheelZoom: false,
      zoomAnimation: false,
      attributionControl: false // This is added later, but order matters
    });
    _map.fitBounds([[24.6, -125.0], [50.0, -65.0]]);

    _layersControl = L.control.layers({
      'Terrain': L.esriTerrain().addTo(_map),
      'Satellite': L.openAerialMap(),
      'Street': L.openStreetMap(),
      'Grayscale': L.esriGrayscale()
    }).addTo(_map);

    // Add Map Controls
    if (!Util.isMobile()) {
      _map.addControl(L.control.attribution());
      _map.addControl(L.control.scale());
      _map.addControl(L.control.fullscreen());
      _map.addControl(L.control.mousePosition());
    }

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

    _loadOverlays();
  };

  /**
   * Load overlays from the layers.json endpoint.
   */
  _loadOverlays = function () {
    var url = _url + '/layers.json';

    Xhr.ajax({
      url: url,
      success: function (data) {
        var overlays;
        overlays = data.parameters.required.type.values;
        overlays.forEach(function (overlay) {
          console.log(overlay);
          // TODO: use "url" and "overlay" object to configure overlay
          // add overlay to layers control using
          _layersControl.addOverlay(RegionsLayer(overlay), name);
        });
      }
    });
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
    _map.removeControl(_layersControl);

    // variables
    _layersControl = null;
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
