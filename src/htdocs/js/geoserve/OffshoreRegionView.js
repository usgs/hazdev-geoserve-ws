'use strict';

var BaseView = require('geoserve/BaseView'),
    Util = require('util/Util');

// Default values to be used by constructor
var _DEFAULTS = {
  header: null,
  noDataMessage: '<p class="alert info">Offshore region data not available.</p>'
};


var OffshoreRegionView = function (params) {
  var _this,
      _initialize;

  params = Util.extend({}, _DEFAULTS, params);
  _this = BaseView(params);

  _initialize = function () {
    _this.addClass('offshore-region-view');
    _this.render();
  };

  _this.render = function () {
    var markup,
        offshoreResponse,
        properties;

    markup = [_this.header];

    try {
      offshoreResponse = _this.model.get('regions').offshore;
      properties = offshoreResponse.features[0].properties;

      markup.push(
        '<dl class="horizontal">' +
          '<dt>Name</dt>' +
            '<dd>' + properties.name + '</dd>' +
        '</dl>'
      );

    } catch (e) {
      markup.push(_this.noDataMessage);
    }

    _this.el.innerHTML = markup.join('');
  };

  /**
   * Destroy all the things.
   */
  _this.destroy = Util.compose(function () {
    _initialize = null;
    _this = null;
  }, _this.destroy);

  _initialize();
  params = null;
  return _this;
};

module.exports = OffshoreRegionView;
