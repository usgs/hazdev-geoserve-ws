'use strict';

var Model = require('mvc/Model'),
    Util = require('util/Util'),
    Xhr = require('util/Xhr');


var OffshoreRegionView = function (options) {
  var _this,
      _initialize;

  options = Util.extend({}, _DEFAULTS, options);

  _initialize = function () {
    _this.render();
  };

  _this.render = function () {

  };

  _this.destroy = Util.compose(function () {
    _initialize = null;
    _this = null;
  }, _this.destroy);

  _initialize()
  options = null;
  return _this;
};

module.exports = OffshoreRegionView;
