'use strict';


var AdminRegionView = require('geoserve/AdminRegionView'),
    AuthoritativeRegionView = require('geoserve/AuthoritativeRegionView'),
    LocationMapView = require('geoserve/LocationMapView'),
    LocationOutputView = require('geoserve/LocationOutputView'),
    NearbyCitiesView = require('geoserve/NearbyCitiesView'),
    NeicCatalogView = require('geoserve/NeicCatalogView'),
    NeicResponseView = require('geoserve/NeicResponseView'),
    OffshoreRegionView = require('geoserve/OffshoreRegionView'),
    TectonicSummaryView = require('geoserve/TectonicSummaryView'),
    TimezoneRegionView = require('geoserve/TimezoneRegionView'),

    View = require('mvc/View'),

    Util = require('util/Util'),
    Xhr = require('util/Xhr');


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
 * @param params {Object}
 *        all options are passed to View().
 * @param params.url {String}
 *        default '/ws/geoserve'
 *        base url for geoserve web service.
 */
var LocationApp = function (params) {
  var _this,
      _initialize,

      _adminRegionView,
      _authoritativeRegionView,
      _locationMapView,
      _locationOutputView,
      _nearbyCitiesView,
      _neicCatalogView,
      _neicResponseView,
      _offshoreRegionView,
      _tectonicSummaryView,
      _timezoneRegionView,
      _url,

      _onLocationChange;


  _this = View(params);

  /**
   * @constructor
   *
   * @param params {Object}
   *      Configuration options. See _DEFAULTS for details.
   */
  _initialize = function (params) {
    var el;

    params = Util.extend({}, _DEFAULTS, params);
    _url = params.url;

    el = _this.el;

    el.innerHTML = '<div class="row">' +
        '<div class="column two-of-three">' +
          '<section class="location-map-view"></section>' +
        '</div>' +
        '<div class="column one-of-three region-view-wrapper">' +
          '<section class="location-output-view"></section>' +
          '<section class="admin-region-view"></section>' +
          '<section class="nearbycities-view"></section>' +
        '</div>' +
      '</div>' +
      '<section class="tectonic-summary-view"></section>' +
      '<div class="row">' +
        '<section class="column one-of-two timezone-region-view"></section>' +
        '<section class="column one-of-two authoritative-region-view">' +
            '</section>' +
      '</div>' +
      '<div class="row">' +
        '<section class="column one-of-two neiccatalog-view"></section>' +
        '<section class="column one-of-two neicresponse-view"></section>' +
        '<section class="column one-of-two offshore-region-view"></section>' +
      '</div>';

    _this.model.on('change:location', _onLocationChange);

    _locationMapView = LocationMapView({
      el: el.querySelector('.location-map-view'),
      model: _this.model,
      url: _url
    });

    _locationOutputView = LocationOutputView({
      el: el.querySelector('.location-output-view'),
      model: _this.model
    });

    _adminRegionView = AdminRegionView({
      el: el.querySelector('.admin-region-view'),
      model: _this.model,
      noDataMessage: ''
    });

    _nearbyCitiesView = NearbyCitiesView({
      el: el.querySelector('.nearbycities-view'),
      header: '<h3>Nearby Cities</h3>',
      model: _this.model,
      noDataMessage: ''
    });

    _authoritativeRegionView = AuthoritativeRegionView({
      el: el.querySelector('.authoritative-region-view'),
      header: '<h3>ANSS Authoritative Region</h3>',
      model: _this.model,
      noDataMessage: '<aside class="no-data-message">Data not available</aside>'
    });

    _timezoneRegionView = TimezoneRegionView({
      el: el.querySelector('.timezone-region-view'),
      header: '<h3>Timezone</h3>',
      model: _this.model,
      noDataMessage: '<aside class="no-data-message">Data not available</aside>'
    });

    _neicCatalogView = NeicCatalogView({
      el: el.querySelector('.neiccatalog-view'),
      header: '<h3>NEIC Catalog Region</h3>',
      model: _this.model,
      noDataMessage: '<aside class="no-data-message">Data not available</aside>'
    });

    _neicResponseView = NeicResponseView({
      el: el.querySelector('.neicresponse-view'),
      header: '<h3>NEIC Response Region</h3>',
      model: _this.model,
      noDataMessage: '<aside class="no-data-message">Data not available</aside>'
    });

    _tectonicSummaryView = TectonicSummaryView({
      el: el.querySelector('.tectonic-summary-view'),
      model: _this.model,
      noDataMessage: ''
    });

    _offshoreRegionView = OffshoreRegionView({
      el: el.querySelector('.offshore-region-view'),
      header: '<h3>Offshore Region View</h3>',
      model: _this.model,
      noDataMessage: '<aside class="no-data-message">Data not available</aside>'
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
    _adminRegionView.destroy();
    _authoritativeRegionView.destroy();
    _locationMapView.destroy();
    _locationOutputView.destroy();
    _nearbyCitiesView.destroy();
    _neicCatalogView.destroy();
    _neicResponseView.destroy();
    _tectonicSummaryView.destroy();
    _timezoneRegionView.destroy();

    // free references
    _adminRegionView = null;
    _authoritativeRegionView = null;
    _locationMapView = null;
    _locationOutputView = null;
    _nearbyCitiesView = null;
    _neicCatalogView = null;
    _neicResponseView = null;
    _offshoreRegionView = null;
    _tectonicSummaryView = null;
    _timezoneRegionView = null;
    _url = null;

    _onLocationChange = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);


  _initialize(params);
  params = null;
  return _this;
};


module.exports = LocationApp;
