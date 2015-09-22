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
			header: '<h3>Admin Region</h3>'
		});
	}
});