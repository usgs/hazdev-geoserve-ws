'use strict';

var Format = require('geoserve/Formatter'),

    View = require('mvc/View'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: 'Neic response data not available.'
};


/**
 * Class: NeicResponseView
 *
 * @param params {Object}
 *      Configuration options. See _DEFAULTS for more details.
 */
var NeicResponseView = function (params) {
  var _this,
      _initialize,

      _header,
      _noDataMessage;


  // Inherit from parent class
  _this = View(params || {});

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
    if (!classes.contains('neic-response-view')) {
      classes.add('neic-response-view');
    }

    _this.render();
  };


  /**
   * Free resources.
   *
   */
  _this.destroy = Util.compose(function () {
    _header = null;
    _noDataMessage = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);

  /**
   * Updates the view to reflect the current state of the model.
   *
   */
  _this.render = function () {
    var markup,
        neicResponse,
        properties;

    markup = [(_header !== null) ? _header : ''];

    try {
      neicResponse = _this.model.get('regions').neicresponse;
      properties = neicResponse.features[0].properties;

      markup.push(
        '<dl>' +
          '<dt>Name</dt>' +
            '<dd>' + properties.name + '</dd>' +
          '<dt>Type</dt>' +
            '<dd>' + properties.type + '</dd>' +
          '<dt>Magnitude</dt>' +
            '<dd>' + Format.formatMagnitude(properties.magnitude) + '</dd>' +
        '</dl>'
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

module.exports = NeicResponseView;
