'use strict';


var View = require('mvc/View');


/**
 * A temporary view to show location, places, and region information.
 */
var LocationOutputView = function (options) {
  var _this;


  _this = View(options);

  _this.render = function () {
    _this.el.innerHTML = '<pre>' +
        JSON.stringify(_this.model.toJSON(), true, 2) +
        '</pre>';
  };


  options = null;
  return _this;
};


module.exports = LocationOutputView;
