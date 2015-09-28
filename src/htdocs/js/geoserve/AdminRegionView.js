'use strict';

var BaseView = require('geoserve/BaseView'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class="alert info">Administrative region not available.</p>'
};


/**
 * Class: AdminRegionView
 *        A view to show current administrative region information.
 *
 * @param params {object}
 *      Configuration options. See _DEFAULTS for more details
 */
var AdminRegionView = function (params) {
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
    _this.addClass('admin-region-view');

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
    var adminRegions,
        markup,
        properties;

    markup = [(_this.header !== null) ? _this.header : ''];

    try {
      adminRegions = _this.model.get('regions').admin;
      properties = adminRegions.features[0].properties;

      markup.push(
        '<dl class="horizontal">' +
          '<dt>ISO</dt>' +
            '<dd>' + properties.iso + '</dd>' +
          '<dt>Country</dt>' +
            '<dd>' + properties.country + '</dd>' +
          '<dt>Region</dt>' +
            '<dd>' + properties.region + '</dd>' +
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

module.exports = AdminRegionView;
