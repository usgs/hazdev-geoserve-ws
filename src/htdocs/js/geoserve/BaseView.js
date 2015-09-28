'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: 'No data available.'
};


/**
 * Class: BaseView
 *
 * @param params {object}
 *      Configuration options. See _DEFAULTS for more details
 */
var BaseView = function (params) {
  var _this,
      _initialize;


  // Inherit from parent class
  _this = View(params || {});

  /**
   * @constructor
   *
   */
  _initialize = function (params) {
    params = Util.extend({}, _DEFAULTS, params);

    _this.header = params.header;
    _this.noDataMessage = params.noDataMessage;
  };


  _this.addClass = function (newClass) {
    var classes;

    classes = _this.el.classList;
    if (!classes.contains(newClass)) {
      classes.add(newClass);
    }
  };


  // Always call the constructor
  _initialize(params);
  params = null;
  return _this;
};

module.exports = BaseView;
