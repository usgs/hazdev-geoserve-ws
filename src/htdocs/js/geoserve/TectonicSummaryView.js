'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: 'Tectonic summary data not available.'
};


/**
 * Class: TectonicSummaryView
 *
 * @param options {Object}
 *        Configuration options. See _DEFAULTS for more details.
 */
var TectonicSummaryView = function (options) {
  var _this,
      _initialize,

      _header,
      _noDataMessage;


  // Inherit from parent class
  _this = View(options || {});

  /**
   * @constructor
   *
   */
  _initialize = function (options) {
    var classes;

    options = Util.extend({}, _DEFAULTS, options);

    _header = options.header;
    _noDataMessage = options.noDataMessage;

    classes = _this.el.classList;
    if (!classes.contains('tectonic-summary-view')) {
      classes.add('tectonic-summary-view');
    }

    _this.render();
  };


  _this.destroy = Util.compose(
    // Sub class destroy method
    function () {
      // Clean up private variables
      _header = null;
      _noDataMessage = null;

      _initialize = null;
      _this = null;
    },
    // Parent class destroy method
    _this.destroy);

  _this.render = function () {
    var markup,
        properties,
        tectonicSummary;

    markup = [(_header !== null) ? _header : ''];

    try {
      tectonicSummary = _this.model.get('regions').tectonicsummary;
      // properties include: name, type and summary
      properties = tectonicSummary.features[0].properties;

      markup.push(properties.summary);
    } catch (e) {
      markup.push('<p class="alert info">' + _noDataMessage + '</p>');
    }

    _this.el.innerHTML = markup.join('');
  };


  // Always call the constructor
  _initialize(options);
  options = null;
  return _this;
};

module.exports = TectonicSummaryView;
