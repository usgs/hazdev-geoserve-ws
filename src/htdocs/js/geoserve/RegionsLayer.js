'use strict';

var L = require('leaflet'),
		Xhr = require('util/Xhr');

var COLORS = [
	'#1f78b4', // teal
	'#ffff99', // yellow
	'#33a02c', // green
	'#e31a1c', // red
	'#ff7f00', // orange
	'#6a3d9a', // purple
	'#b15928' // brown
];

var COLORS_INDEX = 0;

var RegionsLayer = L.GeoJSON.extend({

	initialize: function (url, type) {
		L.GeoJSON.prototype.initialize.call(this, null, {
			'style': {
				'color': COLORS[COLORS_INDEX++ % COLORS.length],
				'fillOpacity': 0.4,
				'opacity': 1,
				'weight': 2,
				'clickable': false
			}
		});

		// overlay data
		this._data = null;
		this._type = type;
		this._url = url;
	},

	onAdd: function (map) {
		L.GeoJSON.prototype.onAdd.call(this, map);

		// fetch data
		if (this._data === null) {
			this._loadData();
		}
	},

	/**
	 * Retreives overlay data from layers.json endpoint
	 */
	_loadData: function () {
		var _this;

		_this = this;

		Xhr.ajax({
			url: this._url + '?type=' + this._type,
			success: function (data) {
				try {
					_this._data = data[_this._type];
					_this.addData(_this._data);
				} catch (e) {
					_this._data = null;
				}
			}
		});
	}

});

module.exports = RegionsLayer;
