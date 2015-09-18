'use strict';

var Util = require('util/Util'),
    View = require('mvc/View');


var _DEFAULTS = {};


/**
 * TODO: leaflet map with location view control to show/set location.
 */
var LocationMapView = function (options) {
  var _this,
      _initialize,

      _button,

      _onButtonClick;


  _this = View(options);

  _initialize = function (options) {
    options = Util.extend({}, _DEFAULTS, options);

    _this.el.innerHTML = '<button>Set Location</button>';
    _button = _this.el.querySelector('button');
    _button.addEventListener('click', _onButtonClick);
  };

  /**
   * Set app location when button is clicked.
   */
  _onButtonClick = function () {
    _this.model.set({
      location: {
        latitude: 34,
        longitude: -118
      }
    });
  };

  /**
   * View destroy method.
   */
  _this.destroy = Util.compose(function () {
    // remove event listeners
    _button.removeEventListener('click', _onButtonClick);

    // free references
    _button = null;
    _onButtonClick = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);


  _initialize(options);
  options = null;
  return _this;
};


module.exports = LocationMapView;
