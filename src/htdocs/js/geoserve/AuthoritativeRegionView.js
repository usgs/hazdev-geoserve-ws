'use strict';

var View = require('mvc/View'),

		Util = require('util/Util');

var DEFAULTS = {
	noDataMessage: 'Authoritative region data, not available.',
	header: null
};

var AuthoritativeRegionView = function (params) {
	var _this,
			_initialize,

			_header,
			_noDataMessage;

	_this = View(params||{});

	_initialize = function (params) {

		params = Util.extend({}, DEFAULTS, params);

		_noDataMessage = params.noDataMessage;
		_header = params.header;

		_this.el.className = 'authoritative-region';
		_this.render();
	};

	_this.render = function () {
		var auth,
				regions,
				markup;

		try {
			auth = regions.authoritative.features[0].properties;
		} catch (e) {
			auth = null;
		}

		if (auth === null) {
			markup = '<p class="alert info">' + _noDataMessage + '</p>';
		} else {
			markup = '<dl>' +
						'<dt>Name</dt>' +
						'<dd>' + auth.properties.name + '</dd>' +
						'<dt>Network</dt>' +
						'<dd>' + auth.properties.network + '</dd>' +
					'</dl>';
		}

		// Do not display blank header
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

module.exports = AuthoritativeRegionView;