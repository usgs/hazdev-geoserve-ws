'use strict';


var View = require('mvc/View'),

    Util = require('util/Util');


var _NO_LOCATION_MESSAGE = '<p class="alert info">Use the map to select a location.</p>';

var _DEFAULTS = {
  header: '<h3>Current Location</h3>'
};


/**
 * A view to show current location information.
 *
 *
 * @param params {Object}
 *      Configuration parameters. See _DEFAULTS for available options.
 */
var LocationOutputView = function (params) {
  var _this,
      _initialize,

      _header,

      _formatLatitude,
      _formatLocation,
      _formatLongitude;


  _this = View(params||{});

  _initialize = function (params) {
    params = Util.extend({}, _DEFAULTS, params);

    _header = params.header;

    _this.el.classList.add('location-output-view');
    _this.render();
  };


  /**
   * Formats a number as a latitude with the specified number of decimals.
   *
   * @param latitude {Number}
   *      The number to be formatted.
   * @param decimals {Integer}
   *      The number of decimals to include in the formatted output.
   *
   * @return {String}
   *      A formatted representation of the input latitude.
   */
  _formatLatitude = function (latitude, decimals) {
    var direction;

    direction = '&deg;N';
    if (latitude < 0) {
      latitude *= -1;
      direction = '&deg;S';
    }

    if (typeof decimals === 'undefined') {
      decimals = 3;
    }

    return latitude.toFixed(decimals) + direction;
  };

  /**
   * @param location {Object}
   *      An object with "latitude" and "longitude" attributes.
   *
   * @return {String}
   *      A formatted representation of the place
   */
  _formatLocation = function (location) {
    var markup = [];

    if (location.place) {
      markup.push('<strong>', location.place, '</strong><small>');
    }

    markup.push(_formatLatitude(location.latitude), ', ',
        _formatLongitude(location.longitude));

    if (location.place) {
      markup.push('</small>');
    }

    return markup.join('');
  };

  /**
   * Formats a number as a longitude with the specified number of decimals.
   *
   * @param longitude {Number}
   *      The number to be formatted.
   * @param decimals {Integer}
   *      The number of decimals to include in the formatted output.
   *
   * @return {String}
   *      A formatted representation of the input longitude.
   */
  _formatLongitude = function (longitude, decimals) {
    var direction;

    direction = '&deg;E';
    if (longitude < 0) {
      longitude *= -1;
      direction = '&deg;W';
    }

    if (typeof decimals === 'undefined') {
      decimals = 3;
    }

    return longitude.toFixed(decimals) + direction;
  };


  _this.render = function () {
    var location,
        markup;

    try {
      location = _this.model.get('location');

      markup = [
        '<p class="alert success">',
          _formatLocation(location),
        '</p>'
      ];
    } catch (e) {
      markup = [_NO_LOCATION_MESSAGE];
    }

    _this.el.innerHTML = ((_header !== null) ? _header : '') + markup.join('');
  };


  _initialize(params);
  params = null;
  return _this;
};


LocationOutputView.NO_LOCATION_MESSAGE = _NO_LOCATION_MESSAGE;


module.exports = LocationOutputView;
