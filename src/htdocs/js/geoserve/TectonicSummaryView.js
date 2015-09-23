'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


var _DEFAULTS = {
  header: null,
  noDataMessage: 'Tectonic summary data not available.'
};

/**
 * Class: TectonicSummaryView
 *
 * @param options {Object}
 *
 */
var TectonicSummaryView = function (options) {
  var _this,
      _initialize,

      _header,
      _noDataMessage;


  _this = View(options || {});

  /**
   * @Constructor
   *
   */
  _initialize = function (options) {
    options = Util.extend({}, _DEFAULTS, options);

    _header = options.header;
    _noDataMessage = options.noDataMessage;

    _this.el.className = 'tectonic-summary-view';

    _this.render();
  };


  _this.destroy = Util.compose(
    // Sub class destroy method
    function () {
      // Clean up private variables
      _header = null;
      _noDataMessage = null;

      _this = null;
    },
    // Parent class destroy method
    _this.destroy);

  _this.render = function () {
    var markup,
        properties,
        tectonicResponse;

    markup = [(_header !== null) ? _header : ''];

    try {
      tectonicResponse = _this.model.get('regions').tectonicsummary;
      // properties include: name, type and summary
      properties = tectonicResponse.features[0].properties;

      markup.push(properties.summary);
    } catch (e) {
      markup.push('<p class="alert info">' + _noDataMessage + '</p>');
    }

    _this.el.innerHTML = markup.join('');
  };

  _initialize(options);
  options = null;
  return _this;
};

module.exports = TectonicSummaryView;
