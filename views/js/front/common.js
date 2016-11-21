/* global $, mymodule */

// Instead of polluting the global namespaces, make a module which will hold all module related
// views, methods and variables.
var MyModule = {};

// Used to hold view instances, e.g. React mounted components, Vue instances, etc.
MyModule.Views = {};

// Holds configuration variables, e.g. API urls, settings, etc.
MyModule.Variables = (function (container) {
  return {
    get: function (key) {
      return typeof container[key] !== 'undefined' ? container[key] : null;
    }
  };
}(mymodule.variables));

// Holds translatable module strings.
MyModule.Translations = (function (container) {
  return {
    get: function (key) {
      return typeof container[key] !== 'undefined' ? container[key] : key;
    }
  };
}(mymodule.translations));

// Holds common module JS functions, e.g. formatting, rounding, debounce, etc.
MyModule.Utils = (function () {
  function debounce(func, delay) {
    var timeout;
    return function () {
      var context = this;
      var args = arguments;
      clearTimeout(timeout);
      timeout = setTimeout(function () {
        func.apply(context, args);
      }, delay);
    };
  }
  function now() {
    return Math.floor(Date.now() / 1000);
  }
  return {
    debounce: debounce,
    now: now
  };
}());

// Module API (AJAX) client
MyModule.API = (function (apiUrl) {
  function resolveErrorMessage(xhr, status, error) {
    /** @var {{ responseText: string, responseJSON: Object }} xhr */
    var data = xhr.responseJSON || {};
    return (data.error && data.error.message) || data.message || data.error || error;
  }

  function makeRequest(action, data, success, error, complete) {
    return $.ajax({
      url: apiUrl,
      timeout: 20000,
      data: $.extend({}, data, { action: action }),
      success: success,
      error: function (xhr, status, errorText) {
        if (typeof error === 'function') {
          error(resolveErrorMessage(xhr, status, errorText), status);
        }
      },
      complete: complete
    });
  }

  function getItems(data, success, error, complete) {
    return makeRequest('get_items', data, success, error, complete);
  }

  return {
    getItems: getItems
  };
}(
  MyModule.Variables.get('api_url')
));

// Technically you should add any logic to this script,
// but sometimes you may need to initialize global View components or something similar.
$(function () {
  // You should keep you logic in a separate file.
});
