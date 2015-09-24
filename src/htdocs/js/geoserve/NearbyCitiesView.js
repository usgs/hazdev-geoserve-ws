'use strict';

var Format = require('geoserve/Formatter'),

    View = require('mvc/View'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: 'Nearby city data not available.'
};


/**
 * Class: NearbyCitiesView
 *
 * @param params {Object}
 *      Configuration options. See _DEFAULTS for more details.
 */
var NearbyCitiesView = function (params) {
  var _this,
      _initialize,

      _header,
      _noDataMessage;


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
    if (!classes.contains('nearby-cities-view')) {
      classes.add('nearby-cities-view');
    }

    _this.render();
  };


  /**
   * Destroy all the things.
   */
  _this.destroy = Util.compose(function () {
    _header = null;
    _noDataMessage = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);

  /**
   * Update map to display current location.
   */
  _this.render = function () {
    var markup,
        nearbycitiesresponse,
        properties,
        i,
        feature;

    markup = [(_header !== null) ? _header : ''];

    try {
      nearbycitiesresponse = _this.model.get('places').event;

      markup.push('<ol class="no-style">');
      for (i = 0; i < nearbycitiesresponse.count; i++) {
        feature = nearbycitiesresponse.features[i];
        properties = feature.properties;
        markup.push(
          '<li>' +
            '<span class="city-distance">' +
              properties.distance +
              'km (' +
                Math.round(Format.kilometersmToMiles(properties.distance)) +
              'mi) ' +
            '</span>' +
            '<span class="direction">' +
              Format.compassWinds(properties.azimuth) + ' of ' +
            '</span>' +
            '<span class="city-name">' +
              properties.name +
            '</span>' +
            '<span class="admin1-name">' +
              ', ' + properties.admin1_name +
            '</span>' +
            '<span class="country-name">' +
              ', ' + properties.country_name +
            '</span>' +
            '<span class="population">' +
              ' population ' +
              Format.numberWithCommas(properties.population) +
            '</span>' +
          '</li>'
        );
      }
      markup.push('</ol>');
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


module.exports = NearbyCitiesView;
