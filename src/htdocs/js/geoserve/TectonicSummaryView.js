'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');

    
var _DEFAULTS = {
};

var TectonicSummaryView = function (options) {
  var _this,
      _initialize;


  _this = View(options || {});

  _initialize = function (options) {
    options = Util.extend({}, _DEFAULTS, options);
  };

  _initialize(options);
  options = null;
  return _this;
};

module.exports = TectonicSummaryView;
