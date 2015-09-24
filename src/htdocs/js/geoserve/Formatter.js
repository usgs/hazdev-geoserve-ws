'use strict';

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
var _formatLatitude = function (latitude, decimals) {
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
var _formatLongitude = function (longitude, decimals) {
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
 * Convert azimuth in degree's into compass points.
 */
var compassWinds = function(azimuth) {
  var fullwind = 22.5,
      directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
          'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW', 'N'];

  return directions[(Math.round((azimuth%360)/fullwind))];
};

/**
 * @param location {Object}
 *      An object with "latitude" and "longitude" attributes.
 *
 * @return {String}
 *      A formatted representation of the place
 */
var formatLocation = function (location) {
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
 * Formats a magnitude number for readability.
 *
 * @param magnitude {Number}
 *      A number representing the magnitude to format.
 *
 * @return {String}
 *      A readable representation of the magnitude.
 */
var formatMagnitude = function (magnitude) {
  // TODO :: Use generic formatter here ...
  return magnitude.toFixed(1);
};

/**
 * Convert Kilometers to miles
 */
var kilometersToMiles = function (km) {
  return (km * 0.621371);
};

/**
 * Put commas into a number for display.
 */
var numberWithCommas = function (x) {
  var parts = x.toString().split('.');

  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

  return parts.join('.');
};

var Formatter = {
  compassWinds: compassWinds,
  formatLocation: formatLocation,
  formatMagnitude: formatMagnitude,
  kilometersToMiles: kilometersToMiles,
  numberWithCommas: numberWithCommas
};

module.exports = Formatter;
