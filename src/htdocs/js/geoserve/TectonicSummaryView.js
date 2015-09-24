'use strict';

var BaseView = require('geoserve/BaseView'),

    Util = require('util/Util');


// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class=alert info">Tectonic summary not available.</p>'
};


/**
 * Class: TectonicSummaryView
 *
 * @param options {Object}
 *        Configuration options. See _DEFAULTS for more details.
 */
var TectonicSummaryView = function (options) {
  var _this,
      _initialize;


  // Inherit from parent class
  options = Util.extend({}, _DEFAULTS, options);
  _this = BaseView(options || {});

  /**
   * @constructor
   *
   */
  _initialize = function () {
    var classes;

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
      _initialize = null;
      _this = null;
    },
    // Parent class destroy method
    _this.destroy);

  _this.render = function () {
    var markup,
        properties,
        tectonicSummary;

    markup = [(_this.header !== null) ? _this.header : ''];

    try {
      tectonicSummary = _this.model.get('regions').tectonicsummary;
      // properties include: name, type and summary
      properties = tectonicSummary.features[0].properties;

      markup.push(properties.summary);
    } catch (e) {
      markup.push(_this.noDataMessage);
    }

    _this.el.innerHTML = markup.join('');
  };


  // Always call the constructor
  _initialize();
  options = null;
  return _this;
};

module.exports = TectonicSummaryView;
