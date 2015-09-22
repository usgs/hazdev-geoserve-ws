'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


var _DEFAULTS = {
  header: '<h2>Tectonic Summary View</h2>'
};

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


  _this.render = function () {
    var markup;

    markup = [];
    
    markup.push(
      'Summary data here'
    );

    _this.el.innerHTML = markup.join('');
  };

  _initialize(options);
  options = null;
  return _this;
};

module.exports = TectonicSummaryView;
