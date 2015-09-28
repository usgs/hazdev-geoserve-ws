'use strict';

var BaseView = require('geoserve/BaseView'),
    Format = require('geoserve/Formatter'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class="alert info">Neic catalog data not available.</p>'
};


/**
 * Class: NeicCatalogView
 *        A view to show current neic catalog information.
 *
 * @param params {Object}
 *      Configuration options. See _DEFAULTS for more details.
 */
var NeicCatalogView = function (params) {
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
    _this.addClass('neic-catalog-view');

    _this.render();
  };


  /**
   * Free resources.
   *
   */
  _this.destroy = Util.compose(function () {
    _initialize = null;
    _this = null;
  }, _this.destroy);

  /**
   * Updates the view to reflect the current state of the model.
   *
   */
  _this.render = function () {
    var markup,
        neicCatalog,
        properties;

    markup = [(_this.header !== null) ? _this.header : ''];

    try {
      neicCatalog = _this.model.get('regions').neiccatalog;
      properties = neicCatalog.features[0].properties;

      markup.push(
        '<dl class="horizontal">' +
          '<dt>Name</dt>' +
            '<dd>' + properties.name + '</dd>' +
          '<dt>Type</dt>' +
            '<dd>' + properties.type + '</dd>' +
          '<dt>Magnitude</dt>' +
            '<dd>' + Format.formatMagnitude(properties.magnitude) + '</dd>' +
        '<dl>'
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

module.exports = NeicCatalogView;
