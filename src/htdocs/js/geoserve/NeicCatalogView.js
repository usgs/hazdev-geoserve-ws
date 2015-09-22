'use strict';

var Model = require('mvc/Model'),
    View = require('mvc/View'),

    Util = require('util/Util');

var NeicCatalogView = function (options) {
  var _this,
      _initialize,

      _data;

  _this = View(options);

  _initialize = function (options) {
    options = options || {};

    _data = options.data || Model({});

    _this.el.classname = 'neic-catalog-view';

  _this.render();
  };

  _this.destroy = Util.compose(function () {
    _data = null;
    _initialize = null;
    _this = null;
  }, _this.destroy);

  _this.render = function () {
    var catalog,
        markup;

  if (_data.get('name') === null && _data.get('type') === null) {
    markup = '<p class="alert info">No NEIC Catalog data</p>';
  } else {
    catalog = _data.get();
    markup = '<dl>' +
          '<dt>Name</dt>' +
          '<dd>' + catalog.name + '</dd>' +
          '<dt>Magnitude</dt>' +
          '<dd>' + catalog.magnitude + '</dd>' +
          '<dt>type</dt>' +
          '<dd>' + catalog.type + '</dd>' +
      '<dl>';
  }

  _this.el.innerHTML = '<h3>Neic Catalog:</h3>' + markup;

  };

  _initialize(options);
  options = null;
  return _this;
};

module.exports = NeicCatalogView;
