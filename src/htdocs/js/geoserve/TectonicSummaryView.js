'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


var _NO_DATA_MESSAGE = '<p class="alert info">No data to display.</p>';


var _DEFAULTS = {
  header: '<h2 class="tectonic-summary-header">Tectonic Summary View</h2>'
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

      _header;


  _this = View(options || {});

  /**
   * @Constructor
   *
   */
  _initialize = function (options) {
    options = Util.extend({}, _DEFAULTS, options);

    _header = options.header;

    _this.render();
  };


  _this.destroy = Util.compose(
    // Sub class destroy method
    function () {
      // Clean up private variables
      _header = null;

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

      markup.push(
        '<div class="tectonic-summary">' +
          properties.summary +
        '</div>'
      );
    } catch (e) {
      markup.push(_NO_DATA_MESSAGE);
    }

    _this.el.innerHTML = markup.join('');
  };

  _initialize(options);
  options = null;
  return _this;
};


TectonicSummaryView.NO_DATA_MESSAGE = _NO_DATA_MESSAGE;

module.exports = TectonicSummaryView;
