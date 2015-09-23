'use strict';

var View = require('mvc/View'),

    Util = require('util/Util');


var _NO_DATA_MESSAGE = '<p class="alert info">No data to display.</p>';


// Default values to be used by constructor
var _DEFAULTS = {
  header: '<h2>Nearby Cities View</h2>'
};


/**
 * Class: Nearby Cities View
 *
 * @param params {Object}
 *      Configuration options. See _DEFAULTS for more details.
 */
var NearbyCitiesView = function (params) {
  var _this,
      _initialize,

      _header;


  // Inherit from parent class
  _this = View(params||{});

  /**
   * @constructor
   *
   */
  _initialize = function (params) {
    params = Util.extend({}, _DEFAULTS, params);

    _header = params.header;

    _this.render();
  };

  _this.compasswinds = function(azimuth) {
    var fullwind = 22.5,
        directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
            'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW', 'N'];

    return directions[(Math.round((azimuth%360)/fullwind))];
  };

  _this.kmToMi = function (km) {
    return (km * 0.621371);
  };

  _this.render = function () {
    var markup,
        nearbycitiesresponse,
        properties,
        i,
        feature;

    markup = [(_header !== null) ? _header : ''];

    try {
      nearbycitiesresponse = _this.model.get('places').event;

      markup.push('<ol class="nearbyCities no-style">');
      for (i = 0; i < nearbycitiesresponse.count; i++) {
        feature = nearbycitiesresponse.features[i];
        properties = feature.properties;
        markup.push(
          '<li>' +
            '<span class="citydistance">' +
              properties.distance +
              'km (' +
              Math.round(this.kmToMi(properties.distance)) + 'mi) ' +
            '</span>' +
            '<span class="direction">' +
              _this.compasswinds(properties.azimuth) + ' of ' +
            '</span>' +
            '<span class="cityname">' +
              properties.name +
            '</span>' +
            '<span class="admin1name">' +
              ', ' + properties.admin1_name +
            '</span>' +
            '<span class="countryname">' +
              ', ' + properties.country_name +
            '</span>' +
            '<span class="population">' +
              ' (' + properties.population + ')' +
            '</span>' +
          '</li>'
        );
      }
      markup.push('</ol>');
    } catch (e) {
      markup.push(_NO_DATA_MESSAGE);
    }

    _this.el.innerHTML = markup.join('');
  };


  // Always call the constructor
  _initialize(params);
  params = null;
  return _this;
};


NearbyCitiesView.NO_DATA_MESSAGE = _NO_DATA_MESSAGE;

module.exports = NearbyCitiesView;
