'use string';

var TectonicSummaryView = require('geoserve/TectonicSummaryView');


var _initialize;

_initialize = function () {

};

TectonicSummaryView({
  header: '<h2>Example with data</h2>',
  el: document.querySelector('#example')
});

TectonicSummaryView({
  header: '<h2>Example without data</h2>',
  el: document.querySelector('#example-nodata')
});


_initialize();
