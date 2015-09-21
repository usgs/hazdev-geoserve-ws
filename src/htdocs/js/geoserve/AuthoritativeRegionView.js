'use strict';

var View = require('mvc/View'),
		Model = require('mvc/Model'),

		Util = require('util/Util');


var AuthoritativeRegionView = function (params) {
	var _this,
			_initialize,

			_data;

	_this = View(params||{});

	_initialize = function (params) {
		params = params || {};

		_data = params.data || Model({});

		_this.el.className = 'authoritative-region';

		_this.render();
	};

	_this.render = function () {
		var region,
				markup;

		if (_data.get('name') === null && _data.get('network') === null) {
			markup = '<p class="alert info">No Authoritative Region Data</p>';
		} else {
			region = _data.get();
			markup = '<dl>' +
						'<dt>Name</dt>' +
						'<dd>' + region.name + '</dd>' +
						'<dt>Network</dt>' +
						'<dd>' + region.network + '</dd>' +
					'</dl>';
		}

		_this.el.innerHTML = '<h3>Authoritative Region</h3>' + markup;
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

module.exports = AuthoritativeRegionView;