'use strict';

var BaseView = require('geoserve/BaseView'),
    Format = require('geoserve/Formatter'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class="alert info">Use the map to select a location.</p>'
};


/**
 * Class: LocationOutputView
 *        A view to show current location information.
 *
 * @param params {Object}
 *      Configuration parameters. See _DEFAULTS for available options.
 */
var LocationOutputView = function (params) {
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
    _this.addClass('location-output-view');

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
   * Render the current state of the model.
   *
   */
  _this.render = function () {
    var location,
        markup;

    markup = [_this.header];

    try {
      location = _this.model.get('location');

      markup.push(
        '<p class="alert success">',
          Format.formatLocation(location),
        '</p>'
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

module.exports = LocationOutputView;
