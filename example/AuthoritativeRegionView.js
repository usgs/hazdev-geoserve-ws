'use strict';

var AuthoritativeRegionView = require('geoserve/AuthoritativeRegionView'),

    Model = require('mvc/Model'),
    Xhr = require('util/Xhr');

Xhr.ajax({
	url: 'data.json',
		success: function (data) {
			AuthoritativeRegionView({
				el: document.querySelector('#example'),
				model: Model({
					regions: data
				}),
				header: '<h3>Authoritative Region</h3>',
				noDataMessage: 'No authoritative region data.'
		});
	}
});