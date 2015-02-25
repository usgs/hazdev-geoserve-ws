(function () {
  'use strict';

  var form = document.querySelector('form.places'),
      el = document.createElement('section'),

      latitude = form.querySelector('#latitude'),
      longitude = form.querySelector('#longitude'),
      maxradiuskm = form.querySelector('#maxradiuskm'),
      minpopulation = form.querySelector('#minpopulation'),
      limit = form.querySelector('#limit'),
      button = form.querySelector('button');


  var disableForm = function () {
    button.disabled = true;
    button.innerHTML = 'Loading ...';
  };

  var enableForm = function () {
    button.disabled = false;
    button.innerHTML = 'Search Places';
  };

  var showResults = function (data) {
    var places = data.features,
        buf = [];

    buf.push('<p>' + places.length + ' matching places</p>');
    buf.push('<table class="tabular"><thead><tr>' +
        '<th>Name</th>' +
        '<th>Admin 1 Code</th>' +
        '<th>Country</th>' +
        '<th>Distance</th>' +
        '<th>Azimuth</th>' +
        '<th>Population</th>' +
        '<th>Lat/Lon</th>' +
        '<th>Elevation</th>' +
        '</tr></thead><tbody>');

    places.forEach(function (place) {
      var props = place.properties,
          coords = place.geometry.coordinates;
      buf.push('<tr>' +
          '<td>' + props.name + '</td>' +
          '<td>' + props.admin1_code + '</td>' +
          '<td>' + props.country_code + '</td>' +
          '<td>' + (props.distance / 1000).toFixed(1) + ' km</td>' +
          '<td>' + props.azimuth + '</td>' +
          '<td>' + props.population + '</td>' +
          '<td>' + coords[1] + '/' + coords[0] + '</td>' +
          '<td>' + coords[2] + '</td>' +
          '</tr>');
    });

    buf.push('</tbody></table>');
    el.innerHTML = buf.join('');
  };

  var onSubmit = function (e) {
    var url,
        xhr;
    // prevent form submit
    e.preventDefault();
    disableForm();
    // load using ajax
    url = 'places' +
        '?latitude=' + encodeURIComponent(latitude.value) +
        '&longitude=' + encodeURIComponent(longitude.value) +
        '&maxradiuskm=' + encodeURIComponent(maxradiuskm.value) +
        '&minpopulation=' + encodeURIComponent(minpopulation.value) +
        '&limit=' + encodeURIComponent(limit.value);
    xhr = new XMLHttpRequest();
    xhr.onload = function () {
      try {
        if (xhr.status !== 200) {
          el.innerHTML = '<pre>' + xhr.responseText + '</pre>';
        } else {
          showResults(JSON.parse(xhr.responseText));
        }
      } finally {
        enableForm();
      }
    };
    xhr.open('get', url, true);
    xhr.send();
  };


  form.parentNode.insertBefore(el, form.nextSibling);
  form.addEventListener('submit', onSubmit);
})();
