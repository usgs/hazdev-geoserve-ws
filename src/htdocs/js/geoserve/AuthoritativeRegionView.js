'use strict';

var BaseView = require('geoserve/BaseView'),

    Util = require('util/Util');

// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class="alert info">Authoritative region not available.<p>'
};


/**
 * Class: AuthoritativeRegionView
 *
 * @param params {object}
 *      Configuration options. See _DEFAULTS for more details
 */
var AuthoritativeRegionView = function (params) {
  var _this,
      _initialize;


  // Inherit from parent class
  params = Util.extend({}, _DEFAULTS, params);
  _this = BaseView(params || {});

  /**
   * @constructor
   *
   */
  _initialize = function () {
    var classes;

    classes = _this.el.classList;
    if (!classes.contains('authoritative-region-view')) {
      classes.add('authoritative-region-view');
    }

    _this.el.classList.add('authoritative-region');
    _this.render();
  };

  _this.render = function () {
    var authoritativeRegions,
        markup,
        properties;

    markup = [(_this.header !== null) ? _this.header : ''];

    try {
      authoritativeRegions = _this.model.get('region').authoritative;
      properties = authoritativeRegions.features[0].properties;

      markup.push(
        '<dl class="horizontal">' +
          '<dt>Name</dt>' +
            '<dd>' + properties.name + '</dd>' +
          '<dt>Network</dt>' +
            '<dd>' + properties.network + '</dd>' +
        '</dl>'
      );
    } catch (e) {
      markup.push(_this.noDataMessage);
    }

    _this.el.innerHTML = markup.join('');
  };

  /**
   * View destroy method.
   */
  _this.destroy = Util.compose(function () {
    _initialize = null;
    _this = null;
  }, _this.destroy);


  // Always call the constructor
  _initialize();
  params = null;
  return _this;
};

module.exports = AuthoritativeRegionView;
