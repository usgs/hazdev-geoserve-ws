'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class="alert info">No data to display data.</p>'
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
    params = Util.extend({}, _DEFAULTS, params);

    _header = params.header;
    _noDataMessage = params.noDataMessage;

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
        neiccatalog,
        properties;

    markup = [(_header !== null) ? _header : ''];

    try {
      neiccatalog = _this.model.get('regions').neiccatalog;

      properties = neiccatalog.features[0].properties;

      markup.push(
        '<dl>' +
          '<dt>Name</dt>' +
            '<dd>' + properties.name + '</dd>' +
          '<dt>type</dt>' +
            '<dd>' + properties.type + '</dd>' +
          '<dt>Magnitude</dt>' +
            '<dd>' + properties.magnitude + '</dd>' +
        '<dl>'
      );
    }
    catch (e) {
      markup.push(_noDataMessage);
    }

  _this.el.innerHTML = markup.join('');
  };


  _initialize(params);
  params = null;
  return _this;
};

module.exports = NeicCatalogView;
