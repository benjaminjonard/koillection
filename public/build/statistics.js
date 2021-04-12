(self["webpackChunkkoillection"] = self["webpackChunkkoillection"] || []).push([["statistics"],{

/***/ "./js/statistics.js":
/*!**************************!*\
  !*** ./js/statistics.js ***!
  \**************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.keys.js */ "./node_modules/core-js/modules/es.object.keys.js");
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_object_values_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.object.values.js */ "./node_modules/core-js/modules/es.object.values.js");
/* harmony import */ var core_js_modules_es_object_values_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_values_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_array_for_each_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.array.for-each.js */ "./node_modules/core-js/modules/es.array.for-each.js");
/* harmony import */ var core_js_modules_es_array_for_each_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_for_each_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_object_entries_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.object.entries.js */ "./node_modules/core-js/modules/es.object.entries.js");
/* harmony import */ var core_js_modules_es_object_entries_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_entries_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_array_is_array_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.array.is-array.js */ "./node_modules/core-js/modules/es.array.is-array.js");
/* harmony import */ var core_js_modules_es_array_is_array_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_is_array_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.symbol.js */ "./node_modules/core-js/modules/es.symbol.js");
/* harmony import */ var core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_symbol_description_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.symbol.description.js */ "./node_modules/core-js/modules/es.symbol.description.js");
/* harmony import */ var core_js_modules_es_symbol_description_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_description_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_es_symbol_iterator_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! core-js/modules/es.symbol.iterator.js */ "./node_modules/core-js/modules/es.symbol.iterator.js");
/* harmony import */ var core_js_modules_es_symbol_iterator_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_iterator_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core-js/modules/es.array.iterator.js */ "./node_modules/core-js/modules/es.array.iterator.js");
/* harmony import */ var core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! core-js/modules/es.array.slice.js */ "./node_modules/core-js/modules/es.array.slice.js");
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! core-js/modules/es.function.name.js */ "./node_modules/core-js/modules/es.function.name.js");
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var core_js_modules_es_array_from_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! core-js/modules/es.array.from.js */ "./node_modules/core-js/modules/es.array.from.js");
/* harmony import */ var core_js_modules_es_array_from_js__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_from_js__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var _translations_config__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./translations/config */ "./js/translations/config.js");
/* harmony import */ var _translations_javascript_en_GB__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./translations/javascript/en-GB */ "./js/translations/javascript/en-GB.js");
/* harmony import */ var _translations_javascript_fr_FR__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./translations/javascript/fr-FR */ "./js/translations/javascript/fr-FR.js");
/* harmony import */ var _translator_min_js__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./translator.min.js */ "./js/translator.min.js");
/* harmony import */ var _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default = /*#__PURE__*/__webpack_require__.n(_translator_min_js__WEBPACK_IMPORTED_MODULE_20__);
/* harmony import */ var echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! echarts/lib/echarts */ "./node_modules/echarts/lib/echarts.js");
/* harmony import */ var echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21___default = /*#__PURE__*/__webpack_require__.n(echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21__);
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }






















 //Echarts components

__webpack_require__(/*! echarts/lib/chart/line */ "./node_modules/echarts/lib/chart/line.js");

__webpack_require__(/*! echarts/lib/chart/bar */ "./node_modules/echarts/lib/chart/bar.js");

__webpack_require__(/*! echarts/lib/chart/heatmap */ "./node_modules/echarts/lib/chart/heatmap.js");

__webpack_require__(/*! echarts/lib/chart/tree */ "./node_modules/echarts/lib/chart/tree.js");

__webpack_require__(/*! echarts/lib/component/calendar */ "./node_modules/echarts/lib/component/calendar.js");

__webpack_require__(/*! echarts/lib/component/tooltip */ "./node_modules/echarts/lib/component/tooltip.js");

__webpack_require__(/*! echarts/lib/component/visualMap */ "./node_modules/echarts/lib/component/visualMap.js");

var monthsLabel = [_translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.january'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.february'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.march'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.april'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.may'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.june'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.july'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.august'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.september'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.october'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.november'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.december')];
var isDarkMode = document.getElementById('settings').dataset.theme === 'dark';
var themeMainHue = isDarkMode ? '#00ce99' : '#009688';
var themeDarkHue = isDarkMode ? '#007C5C' : '#006355';
var themeLightHue = isDarkMode ? '#4DDDB8' : '#1ab0a2';
var themeLightestHue = isDarkMode ? '#b3f0e0' : '#80cbc4';
var itemsEvolutionData = JSON.parse(document.querySelector('#items-evolution-chart').dataset.json);
var calendarsData = JSON.parse(document.querySelector('#calendars').dataset.json);
var treeJson = JSON.parse(document.querySelector('#radial-tree').dataset.json);
loadChart(document.querySelector('#month-days-chart'));
loadChart(document.querySelector('#hours-chart'));
loadChart(document.querySelector('#months-chart'));
loadChart(document.querySelector('#week-days-chart'));

function loadChart(element) {
  var data = JSON.parse(element.dataset.json);
  echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21___default().init(element).setOption({
    tooltip: {
      formatter: function formatter(params) {
        return _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().transChoice('statistics.items_added', params.value);
      }
    },
    color: [themeMainHue],
    xAxis: {
      type: 'category',
      data: data.map(function (element) {
        return element.abscissa;
      }),
      axisLabel: {
        textStyle: {
          color: isDarkMode ? '#f0f0f0' : '#323233'
        }
      },
      axisTick: {
        alignWithLabel: true,
        lineStyle: {
          color: isDarkMode ? '#f0f0f0' : '#323233'
        }
      },
      axisLine: {
        lineStyle: {
          color: isDarkMode ? '#f0f0f0' : '#323233'
        }
      }
    },
    yAxis: {
      splitLine: {
        lineStyle: {
          color: isDarkMode ? '#7d7f82' : '#ccc'
        }
      },
      axisLabel: {
        textStyle: {
          color: isDarkMode ? '#f0f0f0' : '#323233'
        }
      },
      axisTick: {
        lineStyle: {
          color: isDarkMode ? '#f0f0f0' : '#323233'
        }
      },
      axisLine: {
        lineStyle: {
          color: isDarkMode ? '#f0f0f0' : '#323233'
        }
      }
    },
    series: [{
      type: 'bar',
      data: data.map(function (element) {
        return element.count;
      })
    }]
  });
}

echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21___default().init(document.getElementById('items-evolution-chart')).setOption({
  tooltip: {
    trigger: 'axis',
    position: function position(params) {
      return [params[0], '10%'];
    },
    formatter: function formatter(params) {
      return params[0].axisValue + ': ' + _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().transChoice('statistics.items', params[0].data);
    }
  },
  color: [themeMainHue],
  xAxis: {
    type: 'category',
    data: Object.keys(itemsEvolutionData),
    axisLabel: {
      textStyle: {
        color: isDarkMode ? '#f0f0f0' : '#323233'
      }
    },
    axisTick: {
      alignWithLabel: true,
      lineStyle: {
        color: isDarkMode ? '#f0f0f0' : '#323233'
      }
    },
    axisLine: {
      lineStyle: {
        color: isDarkMode ? '#f0f0f0' : '#323233'
      }
    }
  },
  yAxis: {
    type: 'value',
    axisLabel: {
      textStyle: {
        color: isDarkMode ? '#f0f0f0' : '#323233'
      }
    },
    axisTick: {
      lineStyle: {
        color: isDarkMode ? '#f0f0f0' : '#323233'
      }
    },
    axisLine: {
      lineStyle: {
        color: isDarkMode ? '#f0f0f0' : '#323233'
      }
    }
  },
  dataZoom: [{
    handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
    handleSize: '80%',
    handleStyle: {
      color: '#fff',
      shadowBlur: 3,
      shadowColor: 'rgba(0, 0, 0, 0.6)',
      shadowOffsetX: 2,
      shadowOffsetY: 2
    }
  }],
  series: [{
    data: Object.values(itemsEvolutionData),
    type: 'line',
    smooth: true,
    symbol: 'none',
    sampling: 'average',
    areaStyle: {
      normal: {
        color: themeMainHue
      }
    }
  }]
});
Object.entries(calendarsData).forEach(function (_ref) {
  var _ref2 = _slicedToArray(_ref, 2),
      year = _ref2[0],
      yearData = _ref2[1];

  var data = [];
  Object.entries(yearData).forEach(function (_ref3) {
    var _ref4 = _slicedToArray(_ref3, 2),
        index = _ref4[0],
        value = _ref4[1];

    data.push([value[0], "" + value[1]]);
  });
  echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21___default().init(document.getElementById('calendar_' + year)).setOption({
    tooltip: {
      formatter: function formatter(params) {
        return _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().transChoice('statistics.items_added', params.value[1]);
      }
    },
    visualMap: {
      type: 'piecewise',
      orient: 'horizontal',
      right: '215',
      bottom: 'bottom',
      pieces: [{
        min: 31,
        color: themeDarkHue
      }, {
        min: 16,
        max: 30,
        color: themeMainHue
      }, {
        min: 6,
        max: 15,
        color: themeLightHue
      }, {
        min: 1,
        max: 5,
        color: themeLightestHue
      }, {
        min: 0,
        max: 0,
        color: '#ededed'
      }],
      textStyle: {
        color: isDarkMode ? '#f0f0f0' : '#323233'
      }
    },
    calendar: {
      splitLine: {
        show: false
      },
      top: 'middle',
      left: 'center',
      range: year,
      cellSize: 20,
      yearLabel: {
        show: false
      },
      itemStyle: {
        normal: {
          borderWidth: 2,
          borderColor: isDarkMode ? '#36393e' : '#ffffff',
          color: isDarkMode ? '#7d7f82' : '#ededed'
        }
      },
      dayLabel: {
        show: false
      },
      monthLabel: {
        show: true,
        nameMap: monthsLabel,
        color: isDarkMode ? '#f0f0f0' : '#323233'
      }
    },
    series: [{
      type: 'heatmap',
      coordinateSystem: 'calendar',
      calendarIndex: 0,
      data: data
    }]
  });
});
echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21___default().init(document.getElementById('radial-tree')).setOption({
  tooltip: {
    trigger: 'item',
    triggerOn: 'mousemove'
  },
  series: [{
    type: 'tree',
    data: [treeJson],
    layout: 'radial',
    symbol: 'emptyCircle',
    symbolSize: 7,
    initialTreeDepth: -1,
    animationDurationUpdate: 750,
    itemStyle: {
      borderColor: themeMainHue
    },
    lineStyle: {
      color: isDarkMode ? '#4a4b4d' : '#ccc'
    },
    label: {
      color: isDarkMode ? '#a6a7a8' : '#555'
    }
  }]
});

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ "use strict";
/******/ 
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_core-js_modules_es_array_for-each_js-node_modules_core-js_modules_es_arr-f9222b","vendors-node_modules_core-js_modules_es_array_map_js-node_modules_core-js_modules_es_object_v-74b10b","js_translations_config_js-js_translations_javascript_en-GB_js-js_translations_javascript_fr-FR_js"], () => (__webpack_exec__("./js/statistics.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9rb2lsbGVjdGlvbi8uL2pzL3N0YXRpc3RpY3MuanMiXSwibmFtZXMiOlsicmVxdWlyZSIsIm1vbnRoc0xhYmVsIiwiVHJhbnNsYXRvciIsImlzRGFya01vZGUiLCJkb2N1bWVudCIsImdldEVsZW1lbnRCeUlkIiwiZGF0YXNldCIsInRoZW1lIiwidGhlbWVNYWluSHVlIiwidGhlbWVEYXJrSHVlIiwidGhlbWVMaWdodEh1ZSIsInRoZW1lTGlnaHRlc3RIdWUiLCJpdGVtc0V2b2x1dGlvbkRhdGEiLCJKU09OIiwicGFyc2UiLCJxdWVyeVNlbGVjdG9yIiwianNvbiIsImNhbGVuZGFyc0RhdGEiLCJ0cmVlSnNvbiIsImxvYWRDaGFydCIsImVsZW1lbnQiLCJkYXRhIiwiZWNoYXJ0cyIsInNldE9wdGlvbiIsInRvb2x0aXAiLCJmb3JtYXR0ZXIiLCJwYXJhbXMiLCJ2YWx1ZSIsImNvbG9yIiwieEF4aXMiLCJ0eXBlIiwibWFwIiwiYWJzY2lzc2EiLCJheGlzTGFiZWwiLCJ0ZXh0U3R5bGUiLCJheGlzVGljayIsImFsaWduV2l0aExhYmVsIiwibGluZVN0eWxlIiwiYXhpc0xpbmUiLCJ5QXhpcyIsInNwbGl0TGluZSIsInNlcmllcyIsImNvdW50IiwidHJpZ2dlciIsInBvc2l0aW9uIiwiYXhpc1ZhbHVlIiwiT2JqZWN0Iiwia2V5cyIsImRhdGFab29tIiwiaGFuZGxlSWNvbiIsImhhbmRsZVNpemUiLCJoYW5kbGVTdHlsZSIsInNoYWRvd0JsdXIiLCJzaGFkb3dDb2xvciIsInNoYWRvd09mZnNldFgiLCJzaGFkb3dPZmZzZXRZIiwidmFsdWVzIiwic21vb3RoIiwic3ltYm9sIiwic2FtcGxpbmciLCJhcmVhU3R5bGUiLCJub3JtYWwiLCJlbnRyaWVzIiwiZm9yRWFjaCIsInllYXIiLCJ5ZWFyRGF0YSIsImluZGV4IiwicHVzaCIsInZpc3VhbE1hcCIsIm9yaWVudCIsInJpZ2h0IiwiYm90dG9tIiwicGllY2VzIiwibWluIiwibWF4IiwiY2FsZW5kYXIiLCJzaG93IiwidG9wIiwibGVmdCIsInJhbmdlIiwiY2VsbFNpemUiLCJ5ZWFyTGFiZWwiLCJpdGVtU3R5bGUiLCJib3JkZXJXaWR0aCIsImJvcmRlckNvbG9yIiwiZGF5TGFiZWwiLCJtb250aExhYmVsIiwibmFtZU1hcCIsImNvb3JkaW5hdGVTeXN0ZW0iLCJjYWxlbmRhckluZGV4IiwidHJpZ2dlck9uIiwibGF5b3V0Iiwic3ltYm9sU2l6ZSIsImluaXRpYWxUcmVlRGVwdGgiLCJhbmltYXRpb25EdXJhdGlvblVwZGF0ZSIsImxhYmVsIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQ0E7QUFDQTtBQUVBO0NBR0E7O0FBQ0FBLG1CQUFPLENBQUMsd0VBQUQsQ0FBUDs7QUFDQUEsbUJBQU8sQ0FBQyxzRUFBRCxDQUFQOztBQUNBQSxtQkFBTyxDQUFDLDhFQUFELENBQVA7O0FBQ0FBLG1CQUFPLENBQUMsd0VBQUQsQ0FBUDs7QUFDQUEsbUJBQU8sQ0FBQyx3RkFBRCxDQUFQOztBQUNBQSxtQkFBTyxDQUFDLHNGQUFELENBQVA7O0FBQ0FBLG1CQUFPLENBQUMsMEZBQUQsQ0FBUDs7QUFFQSxJQUFJQyxXQUFXLEdBQUcsQ0FDZEMsZ0VBQUEsQ0FBaUIsdUJBQWpCLENBRGMsRUFFZEEsZ0VBQUEsQ0FBaUIsd0JBQWpCLENBRmMsRUFHZEEsZ0VBQUEsQ0FBaUIscUJBQWpCLENBSGMsRUFJZEEsZ0VBQUEsQ0FBaUIscUJBQWpCLENBSmMsRUFLZEEsZ0VBQUEsQ0FBaUIsbUJBQWpCLENBTGMsRUFNZEEsZ0VBQUEsQ0FBaUIsb0JBQWpCLENBTmMsRUFPZEEsZ0VBQUEsQ0FBaUIsb0JBQWpCLENBUGMsRUFRZEEsZ0VBQUEsQ0FBaUIsc0JBQWpCLENBUmMsRUFTZEEsZ0VBQUEsQ0FBaUIseUJBQWpCLENBVGMsRUFVZEEsZ0VBQUEsQ0FBaUIsdUJBQWpCLENBVmMsRUFX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SVgsVUFBSSxFQUFFLFNBRFY7QUFFSTBELHNCQUFnQixFQUFFLFVBRnRCO0FBR0lDLG1CQUFhLEVBQUUsQ0FIbkI7QUFJSXBFLFVBQUksRUFBRUE7QUFKVixLQURJO0FBL0N3RCxHQUFwRTtBQXdESCxDQS9ERDtBQWlFQUMsZ0VBQUEsQ0FBYWxCLFFBQVEsQ0FBQ0MsY0FBVCxDQUF3QixhQUF4QixDQUFiLEVBQXFEa0IsU0FBckQsQ0FBK0Q7QUFDM0RDLFNBQU8sRUFBRTtBQUNMbUIsV0FBTyxFQUFFLE1BREo7QUFFTCtDLGFBQVMsRUFBRTtBQUZOLEdBRGtEO0FBSzNEakQsUUFBTSxFQUFFLENBQ0o7QUFDSVgsUUFBSSxFQUFFLE1BRFY7QUFFSVQsUUFBSSxFQUFFLENBQUNILFFBQUQsQ0FGVjtBQUdJeUUsVUFBTSxFQUFFLFFBSFo7QUFJSWpDLFVBQU0sRUFBRSxhQUpaO0FBS0lrQyxjQUFVLEVBQUUsQ0FMaEI7QUFNSUMsb0JBQWdCLEVBQUUsQ0FBQyxDQU52QjtBQU9JQywyQkFBdUIsRUFBRSxHQVA3QjtBQVFJWixhQUFTLEVBQUU7QUFDUEUsaUJBQVcsRUFBRTVFO0FBRE4sS0FSZjtBQVdJNkIsYUFBUyxFQUFFO0FBQ1BULFdBQUssRUFBRXpCLFVBQVUsR0FBRyxTQUFILEdBQWU7QUFEekIsS0FYZjtBQWNJNEYsU0FBSyxFQUFFO0FBQ0huRSxXQUFLLEVBQUV6QixVQUFVLEdBQUcsU0FBSCxHQUFlO0FBRDdCO0FBZFgsR0FESTtBQUxtRCxDQUEvRCxFIiwiZmlsZSI6InN0YXRpc3RpY3MuanMiLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgJy4vdHJhbnNsYXRpb25zL2NvbmZpZydcbmltcG9ydCAnLi90cmFuc2xhdGlvbnMvamF2YXNjcmlwdC9lbi1HQidcbmltcG9ydCAnLi90cmFuc2xhdGlvbnMvamF2YXNjcmlwdC9mci1GUidcblxuaW1wb3J0IFRyYW5zbGF0b3IgZnJvbSAnLi90cmFuc2xhdG9yLm1pbi5qcydcbmltcG9ydCBlY2hhcnRzIGZyb20gJ2VjaGFydHMvbGliL2VjaGFydHMnXG5cbi8vRWNoYXJ0cyBjb21wb25lbnRzXG5yZXF1aXJlKFwiZWNoYXJ0cy9saWIvY2hhcnQvbGluZVwiKTtcbnJlcXVpcmUoJ2VjaGFydHMvbGliL2NoYXJ0L2JhcicpO1xucmVxdWlyZSgnZWNoYXJ0cy9saWIvY2hhcnQvaGVhdG1hcCcpO1xucmVxdWlyZSgnZWNoYXJ0cy9saWIvY2hhcnQvdHJlZScpO1xucmVxdWlyZSgnZWNoYXJ0cy9saWIvY29tcG9uZW50L2NhbGVuZGFyJyk7XG5yZXF1aXJlKCdlY2hhcnRzL2xpYi9jb21wb25lbnQvdG9vbHRpcCcpO1xucmVxdWlyZSgnZWNoYXJ0cy9saWIvY29tcG9uZW50L3Zpc3VhbE1hcCcpO1xuXG5sZXQgbW9udGhzTGFiZWwgPSBbXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5qYW51YXJ5JyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5mZWJydWFyeScpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMubWFyY2gnKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLmFwcmlsJyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5tYXknKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLmp1bmUnKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLmp1bHknKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLmF1Z3VzdCcpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMuc2VwdGVtYmVyJyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5vY3RvYmVyJyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5ub3ZlbWJlcicpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMuZGVjZW1iZXInKVxuXTtcblxubGV0IGlzRGFya01vZGUgPSAgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NldHRpbmdzJykuZGF0YXNldC50aGVtZSA9PT0gJ2RhcmsnO1xubGV0IHRoZW1lTWFpbkh1ZSA9IGlzRGFya01vZGUgPyAnIzAwY2U5OScgOiAnIzAwOTY4OCc7XG5sZXQgdGhlbWVEYXJrSHVlID0gaXNEYXJrTW9kZSA/ICcjMDA3QzVDJyA6ICcjMDA2MzU1JztcbmxldCB0aGVtZUxpZ2h0SHVlID0gaXNEYXJrTW9kZSA/ICcjNEREREI4JyA6ICcjMWFiMGEyJztcbmxldCB0aGVtZUxpZ2h0ZXN0SHVlID0gaXNEYXJrTW9kZSA/ICcjYjNmMGUwJyA6ICcjODBjYmM0JztcblxubGV0IGl0ZW1zRXZvbHV0aW9uRGF0YSA9IEpTT04ucGFyc2UoZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2l0ZW1zLWV2b2x1dGlvbi1jaGFydCcpLmRhdGFzZXQuanNvbik7XG5sZXQgY2FsZW5kYXJzRGF0YSA9IEpTT04ucGFyc2UoZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2NhbGVuZGFycycpLmRhdGFzZXQuanNvbik7XG5sZXQgdHJlZUpzb24gPSBKU09OLnBhcnNlKGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNyYWRpYWwtdHJlZScpLmRhdGFzZXQuanNvbik7XG5cbmxvYWRDaGFydChkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjbW9udGgtZGF5cy1jaGFydCcpKTtcbmxvYWRDaGFydChkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjaG91cnMtY2hhcnQnKSk7XG5sb2FkQ2hhcnQoZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI21vbnRocy1jaGFydCcpKTtcbmxvYWRDaGFydChkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjd2Vlay1kYXlzLWNoYXJ0JykpO1xuXG5mdW5jdGlvbiBsb2FkQ2hhcnQoZWxlbWVudCkge1xuICAgIGNvbnN0IGRhdGEgPSBKU09OLnBhcnNlKGVsZW1lbnQuZGF0YXNldC5qc29uKTtcbiAgICBlY2hhcnRzLmluaXQoZWxlbWVudCkuc2V0T3B0aW9uKHtcbiAgICAgICAgdG9vbHRpcDoge1xuICAgICAgICAgICAgZm9ybWF0dGVyOiBmdW5jdGlvbiAocGFyYW1zKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFRyYW5zbGF0b3IudHJhbnNDaG9pY2UoJ3N0YXRpc3RpY3MuaXRlbXNfYWRkZWQnLCBwYXJhbXMudmFsdWUpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBjb2xvcjogW3RoZW1lTWFpbkh1ZV0sXG4gICAgICAgIHhBeGlzOiB7XG4gICAgICAgICAgICB0eXBlIDogJ2NhdGVnb3J5JyxcbiAgICAgICAgICAgIGRhdGE6IGRhdGEubWFwKGVsZW1lbnQgPT4gZWxlbWVudC5hYnNjaXNzYSksXG4gICAgICAgICAgICBheGlzTGFiZWw6IHtcbiAgICAgICAgICAgICAgICB0ZXh0U3R5bGU6IHtcbiAgICAgICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBheGlzVGljazoge1xuICAgICAgICAgICAgICAgIGFsaWduV2l0aExhYmVsOiB0cnVlLFxuICAgICAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGF4aXNMaW5lOiB7XG4gICAgICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIHlBeGlzOiB7XG4gICAgICAgICAgICBzcGxpdExpbmU6IHtcbiAgICAgICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnIzdkN2Y4Mic6ICcjY2NjJ1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBheGlzTGFiZWw6IHtcbiAgICAgICAgICAgICAgICB0ZXh0U3R5bGU6IHtcbiAgICAgICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBheGlzVGljazoge1xuICAgICAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGF4aXNMaW5lOiB7XG4gICAgICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIHNlcmllczogW3tcbiAgICAgICAgICAgIHR5cGU6ICdiYXInLFxuICAgICAgICAgICAgZGF0YTogZGF0YS5tYXAoZWxlbWVudCA9PiBlbGVtZW50LmNvdW50KVxuICAgICAgICB9XVxuICAgIH0pO1xufVxuXG5lY2hhcnRzLmluaXQoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2l0ZW1zLWV2b2x1dGlvbi1jaGFydCcpKS5zZXRPcHRpb24oe1xuICAgIHRvb2x0aXA6IHtcbiAgICAgICAgdHJpZ2dlcjogJ2F4aXMnLFxuICAgICAgICBwb3NpdGlvbjogZnVuY3Rpb24gKHBhcmFtcykge1xuICAgICAgICAgICAgcmV0dXJuIFtwYXJhbXNbMF0sICcxMCUnXTtcbiAgICAgICAgfSxcbiAgICAgICAgZm9ybWF0dGVyOiBmdW5jdGlvbiAocGFyYW1zKSB7XG4gICAgICAgICAgICByZXR1cm4gcGFyYW1zWzBdLmF4aXNWYWx1ZSArICc6ICcgKyBUcmFuc2xhdG9yLnRyYW5zQ2hvaWNlKCdzdGF0aXN0aWNzLml0ZW1zJywgcGFyYW1zWzBdLmRhdGEpO1xuICAgICAgICB9XG4gICAgfSxcbiAgICBjb2xvcjogW3RoZW1lTWFpbkh1ZV0sXG4gICAgeEF4aXM6IHtcbiAgICAgICAgdHlwZTogJ2NhdGVnb3J5JyxcbiAgICAgICAgZGF0YTogT2JqZWN0LmtleXMoaXRlbXNFdm9sdXRpb25EYXRhKSxcbiAgICAgICAgYXhpc0xhYmVsOiB7XG4gICAgICAgICAgICB0ZXh0U3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGF4aXNUaWNrOiB7XG4gICAgICAgICAgICBhbGlnbldpdGhMYWJlbDogdHJ1ZSxcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0sXG4gICAgeUF4aXM6IHtcbiAgICAgICAgdHlwZTogJ3ZhbHVlJyxcbiAgICAgICAgYXhpc0xhYmVsOiB7XG4gICAgICAgICAgICB0ZXh0U3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGF4aXNUaWNrOiB7XG4gICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGF4aXNMaW5lOiB7XG4gICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9LFxuICAgIGRhdGFab29tOiBbe1xuICAgICAgICBoYW5kbGVJY29uOiAnTTEwLjcsMTEuOXYtMS4zSDkuM3YxLjNjLTQuOSwwLjMtOC44LDQuNC04LjgsOS40YzAsNSwzLjksOS4xLDguOCw5LjR2MS4zaDEuM3YtMS4zYzQuOS0wLjMsOC44LTQuNCw4LjgtOS40QzE5LjUsMTYuMywxNS42LDEyLjIsMTAuNywxMS45eiBNMTMuMywyNC40SDYuN1YyM2g2LjZWMjQuNHogTTEzLjMsMTkuNkg2Ljd2LTEuNGg2LjZWMTkuNnonLFxuICAgICAgICBoYW5kbGVTaXplOiAnODAlJyxcbiAgICAgICAgaGFuZGxlU3R5bGU6IHtcbiAgICAgICAgICAgIGNvbG9yOiAnI2ZmZicsXG4gICAgICAgICAgICBzaGFkb3dCbHVyOiAzLFxuICAgICAgICAgICAgc2hhZG93Q29sb3I6ICdyZ2JhKDAsIDAsIDAsIDAuNiknLFxuICAgICAgICAgICAgc2hhZG93T2Zmc2V0WDogMixcbiAgICAgICAgICAgIHNoYWRvd09mZnNldFk6IDJcbiAgICAgICAgfVxuICAgIH1dLFxuICAgIHNlcmllczogW3tcbiAgICAgICAgZGF0YTogT2JqZWN0LnZhbHVlcyhpdGVtc0V2b2x1dGlvbkRhdGEpLFxuICAgICAgICB0eXBlOiAnbGluZScsXG4gICAgICAgIHNtb290aDogdHJ1ZSxcbiAgICAgICAgc3ltYm9sOiAnbm9uZScsXG4gICAgICAgIHNhbXBsaW5nOiAnYXZlcmFnZScsXG4gICAgICAgIGFyZWFTdHlsZToge1xuICAgICAgICAgICAgbm9ybWFsOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IHRoZW1lTWFpbkh1ZVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfV1cbn0pO1xuXG5cblxuT2JqZWN0LmVudHJpZXMoY2FsZW5kYXJzRGF0YSkuZm9yRWFjaCgoW3llYXIsIHllYXJEYXRhXSkgPT4ge1xuICAgIHZhciBkYXRhID0gW107XG5cbiAgICBPYmplY3QuZW50cmllcyh5ZWFyRGF0YSkuZm9yRWFjaCgoW2luZGV4LCB2YWx1ZV0pID0+IHtcbiAgICAgICAgZGF0YS5wdXNoKFt2YWx1ZVswXSwgXCJcIiArIHZhbHVlWzFdXSk7XG4gICAgfSk7XG5cbiAgICBlY2hhcnRzLmluaXQoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2NhbGVuZGFyXycgKyB5ZWFyKSkuc2V0T3B0aW9uKHtcbiAgICAgICAgdG9vbHRpcDoge1xuICAgICAgICAgICAgZm9ybWF0dGVyOiBmdW5jdGlvbiAocGFyYW1zKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFRyYW5zbGF0b3IudHJhbnNDaG9pY2UoJ3N0YXRpc3RpY3MuaXRlbXNfYWRkZWQnLCBwYXJhbXMudmFsdWVbMV0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICB2aXN1YWxNYXA6IHtcbiAgICAgICAgICAgIHR5cGU6ICdwaWVjZXdpc2UnLFxuICAgICAgICAgICAgb3JpZW50OiAnaG9yaXpvbnRhbCcsXG4gICAgICAgICAgICByaWdodDogJzIxNScsXG4gICAgICAgICAgICBib3R0b206ICdib3R0b20nLFxuICAgICAgICAgICAgcGllY2VzOiBbXG4gICAgICAgICAgICAgICAge21pbjogMzEsIGNvbG9yOiB0aGVtZURhcmtIdWV9LFxuICAgICAgICAgICAgICAgIHttaW46IDE2LCBtYXg6IDMwLCBjb2xvcjogdGhlbWVNYWluSHVlfSxcbiAgICAgICAgICAgICAgICB7bWluOiA2LCBtYXg6IDE1LCBjb2xvcjogdGhlbWVMaWdodEh1ZX0sXG4gICAgICAgICAgICAgICAge21pbjogMSwgbWF4OiA1LCBjb2xvcjogdGhlbWVMaWdodGVzdEh1ZX0sXG4gICAgICAgICAgICAgICAge21pbjogMCwgbWF4OiAwLCBjb2xvcjogJyNlZGVkZWQnfVxuICAgICAgICAgICAgXSxcbiAgICAgICAgICAgIHRleHRTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgY2FsZW5kYXI6IHtcbiAgICAgICAgICAgIHNwbGl0TGluZToge1xuICAgICAgICAgICAgICAgIHNob3c6IGZhbHNlLFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHRvcDogJ21pZGRsZScsXG4gICAgICAgICAgICBsZWZ0OiAnY2VudGVyJyxcbiAgICAgICAgICAgIHJhbmdlOiB5ZWFyLFxuICAgICAgICAgICAgY2VsbFNpemU6IDIwLFxuICAgICAgICAgICAgeWVhckxhYmVsOiB7c2hvdzogZmFsc2V9LFxuICAgICAgICAgICAgaXRlbVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgbm9ybWFsOiB7XG4gICAgICAgICAgICAgICAgICAgIGJvcmRlcldpZHRoOiAyLFxuICAgICAgICAgICAgICAgICAgICBib3JkZXJDb2xvcjogaXNEYXJrTW9kZSA/ICcjMzYzOTNlJyA6ICcjZmZmZmZmJyxcbiAgICAgICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnIzdkN2Y4MicgOiAnI2VkZWRlZCdcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGRheUxhYmVsOiB7XG4gICAgICAgICAgICAgICAgc2hvdzogZmFsc2VcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBtb250aExhYmVsOiB7XG4gICAgICAgICAgICAgICAgc2hvdzogdHJ1ZSxcbiAgICAgICAgICAgICAgICBuYW1lTWFwOiBtb250aHNMYWJlbCxcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIHNlcmllczogW1xuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHR5cGU6ICdoZWF0bWFwJyxcbiAgICAgICAgICAgICAgICBjb29yZGluYXRlU3lzdGVtOiAnY2FsZW5kYXInLFxuICAgICAgICAgICAgICAgIGNhbGVuZGFySW5kZXg6IDAsXG4gICAgICAgICAgICAgICAgZGF0YTogZGF0YVxuICAgICAgICAgICAgfVxuICAgICAgICBdXG4gICAgfSk7XG59KTtcblxuZWNoYXJ0cy5pbml0KGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdyYWRpYWwtdHJlZScpKS5zZXRPcHRpb24oe1xuICAgIHRvb2x0aXA6IHtcbiAgICAgICAgdHJpZ2dlcjogJ2l0ZW0nLFxuICAgICAgICB0cmlnZ2VyT246ICdtb3VzZW1vdmUnXG4gICAgfSxcbiAgICBzZXJpZXM6IFtcbiAgICAgICAge1xuICAgICAgICAgICAgdHlwZTogJ3RyZWUnLFxuICAgICAgICAgICAgZGF0YTogW3RyZWVKc29uXSxcbiAgICAgICAgICAgIGxheW91dDogJ3JhZGlhbCcsXG4gICAgICAgICAgICBzeW1ib2w6ICdlbXB0eUNpcmNsZScsXG4gICAgICAgICAgICBzeW1ib2xTaXplOiA3LFxuICAgICAgICAgICAgaW5pdGlhbFRyZWVEZXB0aDogLTEsXG4gICAgICAgICAgICBhbmltYXRpb25EdXJhdGlvblVwZGF0ZTogNzUwLFxuICAgICAgICAgICAgaXRlbVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgYm9yZGVyQ29sb3I6IHRoZW1lTWFpbkh1ZSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjNGE0YjRkJyA6ICcjY2NjJ1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGxhYmVsOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2E2YTdhOCcgOiAnIzU1NSdcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIF1cbn0pO1xuXG4iXSwic291cmNlUm9vdCI6IiJ9