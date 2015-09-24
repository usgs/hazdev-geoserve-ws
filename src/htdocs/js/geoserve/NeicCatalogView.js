'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: 'Neic catalog data not available.'
};


/**
 * Class: NeicCatalogView
 *
 * @param params {Object}
 *      Configuration options. See _DEFAULTS for more details.
 */
var NeicCatalogView = function (params) {
  var _this,
      _initialize,

      _header,
      _noDataMessage,

      _formatMagnitude;


  // Inherit from parent class
  _this = View(params||{});

  /**
  * @constructor
  *
  */
  _initialize = function (params) {
    var classes;
    
    params = Util.extend({}, _DEFAULTS, params);

    _header = params.header;
    _noDataMessage = params.noDataMessage;

    classes = _this.el.classList;
    if (!classes.contains('neic-catalog-view')) {
      classes.add('neic-catalog-view');
    }

    _this.render();
  };

  /**
   * Formats a magnitude number for readability.
   *
   * @param magnitude {Number}
   *      A number representing the magnitude to format.
   *
   * @return {String}
   *      A readable representation of the magnitude.
   */
  _formatMagnitude = function (magnitude) {
    // TODO :: Use generic formatter here ...
    return magnitude.toFixed(1);
  };

  /**
   * Free resources.
   *
   */
  _this.destroy = Util.compose(function () {
    _header = null;
    _noDataMessage = null;

    _formatMagnitude = null;

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

    markup = [(_header !== null) ? _header : ''];

    try {
      neicCatalog = _this.model.get('regions').neiccatalog;
      properties = neicCatalog.features[0].properties;

      markup.push(
        '<dl>' +
          '<dt>Name</dt>' +
            '<dd>' + properties.name + '</dd>' +
          '<dt>type</dt>' +
            '<dd>' + properties.type + '</dd>' +
          '<dt>Magnitude</dt>' +
            '<dd>' + _formatMagnitude(properties.magnitude) + '</dd>' +
        '<dl>'
      );
    } catch (e) {
      markup.push(
        '<p class="alert info">' +
          _noDataMessage +
        '</p>'
      );
    }

    _this.el.innerHTML = markup.join('');
  };


  // Always call the constructor
  _initialize(params);
  params = null;
  return _this;
};

module.exports = NeicCatalogView;
