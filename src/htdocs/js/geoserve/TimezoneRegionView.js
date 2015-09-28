'use strict';

var BaseView = require('geoserve/BaseView'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class="alert info">Time zone data not available.</p>'
};


/**
 * Class: TimezoneRegionView
 *        A view to show current timezone information.
 *
 * @param params {object}
 *      Configuration options. See _DEFAULTS for more details
 */
var TimezoneRegionView = function (params) {
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
    _this.addClass('timezone-region-view');

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
   * Updates the view to reflect the current state of the model.
   */
  _this.render = function () {
    var markup,
        properties,
        timeZoneRegions;

    markup = [_this.header];

    try {
      timeZoneRegions = _this.model.get('regions').timezone;
      properties = timeZoneRegions.features[0].properties;

      markup.push(
        '<dl class="horizontal">' +
          '<dt>Time Zone</dt>' +
            '<dd>' + properties.timezone + '</dd>' +
          '<dt>Standard Offset</dt>' +
            '<dd>' + properties.standardoffset + '</dd>' +
          '<dt>DST Start</dt>' +
            '<dd>' + properties.dststart + '</dd>' +
          '<dt>DST End</dt>' +
            '<dd>' + properties.dstend + '</dd>' +
          '<dt>DST Offset</dt>' +
            '<dd>' + properties.dstoffset + '</dd>' +
        '</dl>'
      );
    } catch (e) {
      markup.push(_this.noDataMessage);
    }

    _this.el.innerHTML = markup.join('');
  };


  // Always call the constructor
  _initialize();
  params = null;
  return _this;
};

module.exports = TimezoneRegionView;
