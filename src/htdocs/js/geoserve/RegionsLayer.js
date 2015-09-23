'use strict';

var L = require('leaflet'),
		Xhr = require('util/Xhr');

var RegionsLayer = L.GeoJSON.extend({

	initialize: function (layer) {
		var layersUrl;

		L.GeoJSON.prototype.initialize();

		// TODO, remove url pass in URL on layer selection
		layersUrl = 'layers.json';

		// layers data
		this._data = null;
		this._type = layer.name;

		// url to layer service
		this._layersUrl = layersUrl;
	},


	onAdd: function (map) {
		L.GeoJSON.prototype.onAdd.call(this, map);

		if (this._data) {
			L.geoJson(this._data).addTo(map);
		} else {
			// fetch data
			getGeoJsonLayer(map);
		}
	},


	// turns data into feature array
	getGeoJsonLayer: function (map) {
		var _this;

		_this = this;

		// fetch data if it has not already been fetched
		Xhr.ajax({
			url: this._layersUrl + '?type=' + this._type,
			success: function (data) {
				// TODO, first check this._layers
				try {
					_this._data = data[_this._type].features;
				} catch (e) {
					_this._data = null;
				}

			L.geoJson(_this._data).addTo(map);
			}
		});
	}

});

module.exports = RegionsLayer;