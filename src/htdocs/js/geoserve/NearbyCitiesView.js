'use strict';

var BaseView = require('geoserve/BaseView'),
    Format = require('geoserve/Formatter'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class="alert info">Nearby city data not available.</p>'
};


/**
 * Class: NearbyCitiesView
 *        A view to show current neary city information.
 *
 * @param params {Object}
 *      Configuration options. See _DEFAULTS for more details.
 */
var NearbyCitiesView = function (params) {
  var _this,
      _initialize;


  // Inherit from parent class
  params = Util.extend({}, _DEFAULTS, params);
  _this = BaseView(params);

  /**
   * @constructor
   *
   */
  _initialize = function () {
    _this.addClass('nearby-cities-view');

    _this.render();
  };


  /**
   * Free resources using "View" destroy method.
   *
   */
  _this.destroy = Util.compose(function () {
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

    markup = [_this.header];

    try {
      nearbycitiesresponse = _this.model.get('places').event;

      markup.push('<ol class="no-style">');
      for (i = 0; i < nearbycitiesresponse.count; i++) {
        feature = nearbycitiesresponse.features[i];
        properties = feature.properties;
        markup.push(
          '<li>' +
            '<span class="name">' +
              properties.name + ', ' +
              properties.admin1_name + ', ' +
              properties.country_name +
            '</span>' +
            '<aside class="distance">' +
              Format.formatDistance(properties.distance) + ' ' +
              Format.compassWinds(properties.azimuth) +
            '</aside>' +
            '<aside class="population">Population: ' +
              Format.numberWithCommas(properties.population) +
            '</aside>' +
          '</li>'
        );
      }
      markup.push('</ol>');
    } catch (e) {
      markup = [_this.noDataMessage];
    }

    _this.el.innerHTML = markup.join('');
  };


  // Always call the constructor
  _initialize();
  params = null;
  return _this;
};


module.exports = NearbyCitiesView;
