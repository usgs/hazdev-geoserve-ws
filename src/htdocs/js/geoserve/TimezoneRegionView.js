'use strict';

var View = require('mvc/View'),
    Model = require('mvc/Model'),

    Util = require('util/Util');

var TimezoneRegionView = function (params) {
  var _this,
      _initialize,

      _data;

  _this = View(params || {});

  _initialize = function (params) {
    params = params || {};

    _data = params.regions || Model({});

    _this.render();
  };

  _this.render = function () {
    var timeData;

    timeData = _data.get('region').timezone.features;

    if (timeData === null) {
      _this.el.innerHTML =[
        '<h3>',
          'Time Zone Info',
        '</h3>',
        '<p>Time zone data unavailable</p>'
      ].join('');
    } else {
      _this.el.innerHTML = [
        '<h3>',
          'Time Zone Info',
        '</h3>',
        '<dl>',
          '<dt>Time Zone</dt>',
            '<dd>',timeData.name,'</dd>',
          '<dt>Offset</td>',
            '<dd>',timeData.offset,'</dd>',
          '<dt>DST Start</dt>',
            '<dd>',timeData.dststart,'</dd>',
          '<dt>DST End</dt>',
            '<dd>',timeData.dstend,'</dd>',
          '<dt>DST Offset</dt>',
            '<dd>',timeData.dstoffset,'</dd>',
        '</dl>'
      ].join('');
    }
  };

  _this.destroy = Util.compose(function () {
    _data = null;
    _initialize = null;
    _this = null;
  }, _this.destroy);

  _initialize(params);
  params = null;
  return _this;
};

module.exports = TimezoneRegionView;
