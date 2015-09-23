'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


var _NO_DATA_MESSAGE = '<p class="alert info">No data to display.</p>';


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: _NO_DATA_MESSAGE
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


  _formatMagnitude = function (magnitude) {
    // TODO :: Use generic formatter here ...
    return magnitude.toFixed(1);
  };


  _this.destroy = Util.compose(function () {
    _header = null;
    _noDataMessage = null;

    _formatMagnitude = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);

  _this.render = function () {
    var markup,
        neicresponse,
        properties;

    markup = [(_header !== null) ? _header : ''];

    try {
      neicresponse = _this.model.get('regions').neicresponse;
      properties = neicresponse.features[0].properties;

      markup.push(
        '<dl>' +
          '<dt>Name</dt>' +
            '<dd>' + properties.name + '</dd>' +
          '<dt>Type</dt>' +
            '<dd>' + properties.type + '</dd>' +
          '<dt>Magnitude</dt>' +
            '<dd>' + _formatMagnitude(properties.magnitude) + '</dd>' +
        '</dl>'
      );
    } catch (e) {
      markup.push(_noDataMessage);
    }

    _this.el.innerHTML = markup.join('');
  };


  // Always call the constructor
  _initialize(params);
  params = null;
  return _this;
};


NeicResponseView.NO_DATA_MESSAGE = _NO_DATA_MESSAGE;

module.exports = NeicResponseView;
