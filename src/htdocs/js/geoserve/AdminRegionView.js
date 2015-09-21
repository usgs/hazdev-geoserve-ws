'use strict';

var View = require('mvc/View'),
		Model = require('mvc/Model'),

		Util = require('util/Util');


var AdminRegionView = function (params) {
	var _this,
			_initialize,

			_data;

	_this = View(params||{});

	_initialize = function (params) {
		params = params || {};

		_data = params.data || Model({});

		_this.el.className = 'administrative-region';

		_this.render();
	};

	_this.render = function () {
		var region,
				markup;

		if (_data.get('country') === null) {
			markup = '<p class="alert info">No Administrative Region Data</p>';
		} else {
			region = _data.get();
			markup = '<dl>' +
						'<dt>ISO</dt>' +
						'<dd>' + region.iso + '</dd>' +
						'<dt>Country</dt>' +
						'<dd>' + region.country + '</dd>' +
						'<dt>Region</dt>' +
						'<dd>' + region.region + '</dd>' +
					'</dl>';
		}

		_this.el.innerHTML = '<h3>Administrative Region</h3>' + markup;
	};

  /**
   * View destroy method.
   */
  _this.destroy = Util.compose(function () {
    _data = null;
    _initialize = null;
    _this = null;
  }, _this.destroy);


	_initialize(params);
	params = null;
	return _this;

};

module.exports = AdminRegionView;