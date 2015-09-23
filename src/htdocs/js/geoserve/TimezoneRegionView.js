'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');

var _DEFAULTS = {
  header: null
};

var TimezoneRegionView = function (params) {
  var _this,
      _initialize,

      _header;

  _this = View(params || {});

  _initialize = function () {
    params = Util.extend({}, _DEFAULTS, params);

    _header = params.header;

    _this.el.className = 'timezone-region';
    _this.render();
  };

  _this.render = function () {
    var markup,
        regions,
        timeData;

    timeData = null;
    regions = _this.model.get('regions');

    try {
      timeData = regions.timezone.features[0].properties;
    } catch (e) {
      markup = '<p class="alert info">Time zone data unavailable.</p>';
    }

    if (timeData !== null) {
      markup = '<dl>' +
          '<dt>Time Zone</dt>' +
            '<dd>' + timeData.timezone + '</dd>' +
          '<dt>Standard Offset</td>' +
            '<dd>' + timeData.standardoffset + '</dd>' +
          '<dt>DST Start</dt>' +
            '<dd>' + timeData.dststart + '</dd>' +
          '<dt>DST End</dt>' +
            '<dd>' + timeData.dstend + '</dd>' +
          '<dt>DST Offset</dt>' +
            '<dd>' + timeData.dstoffset + '</dd>' +
        '</dl>';
    }

    _this.el.innerHTML = _header + markup;
  };

  /**
   * Destroy all the things
   */
  _this.destroy = Util.compose(function () {
    _initialize = null;
    _this = null;
  }, _this.destroy);

  _initialize();
  params = null;
  return _this;
};

module.exports = TimezoneRegionView;
