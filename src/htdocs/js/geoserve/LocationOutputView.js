'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: 'Use the map to select a location.'
};


/**
 * Class: LocationOutputView
 *        A view to show current location information.
 *
 * @param params {Object}
 *      Configuration parameters. See _DEFAULTS for available options.
 */
var LocationOutputView = function (params) {
  var _this,
      _initialize,

      _header,
      _noDataMessage,

      _formatLatitude,
      _formatLocation,
      _formatLongitude;


  // Inherit from parent class
  _this = View(params||{});

  /**
   * @constructor
   *
   */
  _initialize = function (params) {
    var classes;

    params = Util.extend({}, _DEFAULTS, params);

    _header = params.header;
    _noDataMessage = params.noDataMessage;

    classes = _this.el.classList;
    if (!classes.contains('location-output-view')) {
      classes.add('location-output-view');
    }

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


  /**
   * Frees resources.
   *
   */
  _this.destroy = Util.compose(function () {
      _header = null;
      _noDataMessage = null;

      _formatLatitude = null;
      _formatLocation = null;
      _formatLongitude = null;

      _initialize = null;
      _this = null;
  }, _this.destroy);

  /**
   * Render the current state of the model.
   *
   */
  _this.render = function () {
    var location,
        markup;

    markup = [(_header !== null) ? _header : ''];

    try {
      location = _this.model.get('location');

      markup.push(
        '<p class="alert success">',
          _formatLocation(location),
        '</p>'
      );
    } catch (e) {
      markup.push(
        '<p class="alert info">' +
          _noDataMessage +
        '</p>'
      );
    }

    _this.el.innerHTML = markup.join('');
  };


  // Always call the constructor
  _initialize(params);
  params = null;
  return _this;
};


module.exports = LocationOutputView;
