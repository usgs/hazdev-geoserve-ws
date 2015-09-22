/* global MOUNT_PATH */
'use strict';


var LocationApp = require('geoserve/LocationApp');


LocationApp({
  el: document.querySelector('#location'),
  url: MOUNT_PATH
});
