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
		var auth,
				region,
				markup;

		auth = null;

		if (_data && _data.get('region')) {
			region = _data.get('region');

			if (region.authoritative &&
					region.authoritative.features &&
					region.authoritative.features.length !== 0) {
				auth = region.authoritative.features[0];
			}
		}

		if (auth === null) {
			markup = '<p class="alert info">No Authoritative Region Data</p>';
		} else {
			markup = '<dl>' +
						'<dt>Name</dt>' +
						'<dd>' + auth.properties.name + '</dd>' +
						'<dt>Network</dt>' +
						'<dd>' + auth.properties.network + '</dd>' +
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