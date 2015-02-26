/* global HOST_URL_PREFIX, MOUNT_PATH */

(function () {
  'use strict';

  var form = document.querySelector('form.places'),
      el = document.querySelector('.places-search'),

      latitude = form.querySelector('#latitude'),
      longitude = form.querySelector('#longitude'),
      maxradiuskm = form.querySelector('#maxradiuskm'),
      minpopulation = form.querySelector('#minpopulation'),
      limit = form.querySelector('#limit'),
      button = form.querySelector('button'),
      urlEl = el.querySelector('.search-url'),
      resultsEl = el.querySelector('.search-results')
      ;


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

    buf.push(
        '<header>' +
          '<h3>Places Returned</h3>' +
          '<small class="results-meta">' + places.length + ' matching places</small>' +
        '</header>');

    buf.push('<pre><code>',
        JSON.stringify(data, null, '    '),
        '</pre></code>'
    );
    resultsEl.innerHTML = buf.join('');
  };

  var onSubmit = function (e) {
    var url,
        xhr;
    // prevent form submit
    e.preventDefault();
    disableForm();
    // load using ajax
    url = generateUrl();
    xhr = new XMLHttpRequest();
    xhr.onload = function () {
      try {
        if (xhr.status !== 200) {
          resultsEl.innerHTML = '<pre>' + xhr.responseText + '</pre>';
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

  var showUrl = function () {
    var url = HOST_URL_PREFIX + MOUNT_PATH + '/'+ generateUrl();
    urlEl.innerHTML = '<header><h3>Places URL</h3></header>' +
        '<pre><code>' +
          '<a href="' + url + '">' + url + '</a>' +
        '</code></pre>';
  };

  var generateUrl = function () {
    var url = 'places' +
      '?latitude=' + encodeURIComponent(latitude.value) +
      '&longitude=' + encodeURIComponent(longitude.value) +
      '&maxradiuskm=' + encodeURIComponent(maxradiuskm.value) +
      '&minpopulation=' + encodeURIComponent(minpopulation.value) +
      '&limit=' + encodeURIComponent(limit.value);

    return url;
  };




  //form.parentNode.insertBefore(el, form.nextSibling);
  form.addEventListener('submit', onSubmit);
  form.addEventListener('submit', showUrl);
})();
