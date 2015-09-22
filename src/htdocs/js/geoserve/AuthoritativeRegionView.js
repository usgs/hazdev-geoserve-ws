'use strict';

var View = require('mvc/View'),

		Util = require('util/Util');


var AuthoritativeRegionView = function (params) {
	var _this,
			_initialize;

	_this = View(params||{});

	_initialize = function () {
		_this.el.className = 'authoritative-region';
		_this.render();
	};

	_this.render = function () {
		var auth,
				regions,
				markup;

		auth = null;

		if (_this.model.get('regions')) {
			regions = _this.model.get('regions');

			if (regions.authoritative &&
					regions.authoritative.features &&
					regions.authoritative.features.length !== 0) {
				auth = regions.authoritative.features[0];
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
    _initialize = null;
    _this = null;
  }, _this.destroy);


	_initialize();
	params = null;
	return _this;

};

module.exports = AuthoritativeRegionView;