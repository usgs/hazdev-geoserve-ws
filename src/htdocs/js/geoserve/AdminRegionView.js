'use strict';

var View = require('mvc/View'),
		Util = require('util/Util');

var NO_DATA_MESSAGE = 'No Data Available';

var AdminRegionView = function (params) {
	var _this,
			_initialize,

			_header,
			_noDataMessage;

	_this = View(params||{});

	_initialize = function (params) {

		_noDataMessage = params.noDataMessage || NO_DATA_MESSAGE;
		_header = params.header;

		_this.el.className = 'administrative-region';

		_this.render();
	};

	_this.render = function () {
		var admin,
				markup;

		try {
			admin = _this.model.get('regions').admin.features[0].properties;
		} catch (e) {
			admin = null;
		}

		if (admin === null) {
			markup = '<p class="alert info">' + _noDataMessage + '</p>';
		} else {
			markup = '<dl>' +
						'<dt>ISO</dt>' +
						'<dd>' + admin.iso + '</dd>' +
						'<dt>Country</dt>' +
						'<dd>' + admin.country + '</dd>' +
						'<dt>Region</dt>' +
						'<dd>' + admin.region + '</dd>' +
					'</dl>';
		}

		// Do not display a blank header
		if (_header) {
			markup = _header + markup;
		}

		_this.el.innerHTML = markup;
	};

	/**
	 * View destroy method.
	 */
	_this.destroy = Util.compose(function () {
		_header = null;
		_noDataMessage = null;

		_initialize = null;
		_this = null;
	}, _this.destroy);


	_initialize(params);
	params = null;
	return _this;

};

module.exports = AdminRegionView;