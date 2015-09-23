'use strict';

var AdminRegionView = require('geoserve/AdminRegionView'),

		Model = require('mvc/Model'),
		Xhr = require('util/Xhr');

Xhr.ajax({
	url: 'regions.json',
	success: function (data) {
		AdminRegionView({
			el: document.querySelector('#example'),
			model: Model({
				regions: data
			}),
			header: '<h3>Administrative Region (With Data)</h3>'
		});

		AdminRegionView({
			el: document.querySelector('#example2'),
			model: Model({
				regions: null
			}),
			header: '<h3>Administrative Region (Without Data)</h3>'
		});
	}
});