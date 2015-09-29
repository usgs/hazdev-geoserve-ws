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
    var header;

    params = Util.extend({}, _DEFAULTS, params);

    // Make sure the header isn't null.
    header = params.header;
    _this.header = (header !== null) ? header : '';

    _this.noDataMessage = params.noDataMessage;
  };


  /**
   * Add a class to the view container.
   *
   * @param newClass {String}
   *      The name of the class to be added to the container.
   */
  _this.addClass = function (newClass) {
    var classes;

    classes = _this.el.classList;
    if (!classes.contains(newClass)) {
      classes.add(newClass);
    }
  };

  /**
   * Free resources using "View" destroy method.
   *
   */
  _this.destroy = Util.compose(function () {
      _initialize = null;
      _this = null;
  }, _this.destroy);


  // Always call the constructor
  _initialize(params);
  params = null;
  return _this;
};

module.exports = BaseView;
