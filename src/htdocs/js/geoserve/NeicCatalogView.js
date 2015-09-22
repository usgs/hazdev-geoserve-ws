'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');

var _NO_DATA_MESSAGE = '<p class="alert info">No NEIC Catalog data.</p>';

// Default values to be used by constructor
var _DEFAULTS = {
  header: '<h2>NEIC Catalog View</h2>'
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

  _this.el.className = 'neic-catalog';

  _this.render();
  };

  _formatMagnitude = function (magnitude) {
    // TODO :: Use generic formatter here ...
    return magnitude.toFixed(1);
  };

  _this.destroy = Util.compose(function () {
    _header = null;
    _formatMagnitude = null;
  }, _this.destroy);

  _this.render = function () {
    var markup,
        neiccatalog,
        properties;

    markup = [(_header !== null) ? _header : ''];

    try {
      neiccatalog = params.model.get('regions').neiccatalog;

      properties = neiccatalog.features[0].properties;

      markup.push( '<dl>' +
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
      markup.push(_NO_DATA_MESSAGE);
    }

  _this.el.innerHTML = markup.join('');

  };

  _initialize(params);
  params = null;
  return _this;
};

NeicCatalogView.NO_DATA_MESSAGE = _NO_DATA_MESSAGE;

module.exports = NeicCatalogView;
