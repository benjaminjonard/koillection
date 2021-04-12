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
var statisticHolder = document.querySelector('.statistics-holder');
var isDarkMode = statisticHolder.dataset.isDarkMode == 1 ? true : false;
var themeMainHue = statisticHolder.dataset.themeMainHue;
var themeDarkHue = statisticHolder.dataset.themeDarkHue;
var themeLightHue = statisticHolder.dataset.themeLightHue;
var themeLightestHue = statisticHolder.dataset.themeLightestHue;
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
      }]
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9rb2lsbGVjdGlvbi8uL2pzL3N0YXRpc3RpY3MuanMiXSwibmFtZXMiOlsicmVxdWlyZSIsIm1vbnRoc0xhYmVsIiwiVHJhbnNsYXRvciIsInN0YXRpc3RpY0hvbGRlciIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsImlzRGFya01vZGUiLCJkYXRhc2V0IiwidGhlbWVNYWluSHVlIiwidGhlbWVEYXJrSHVlIiwidGhlbWVMaWdodEh1ZSIsInRoZW1lTGlnaHRlc3RIdWUiLCJpdGVtc0V2b2x1dGlvbkRhdGEiLCJKU09OIiwicGFyc2UiLCJqc29uIiwiY2FsZW5kYXJzRGF0YSIsInRyZWVKc29uIiwibG9hZENoYXJ0IiwiZWxlbWVudCIsImRhdGEiLCJlY2hhcnRzIiwic2V0T3B0aW9uIiwidG9vbHRpcCIsImZvcm1hdHRlciIsInBhcmFtcyIsInZhbHVlIiwiY29sb3IiLCJ4QXhpcyIsInR5cGUiLCJtYXAiLCJhYnNjaXNzYSIsImF4aXNMYWJlbCIsInRleHRTdHlsZSIsImF4aXNUaWNrIiwiYWxpZ25XaXRoTGFiZWwiLCJsaW5lU3R5bGUiLCJheGlzTGluZSIsInlBeGlzIiwic3BsaXRMaW5lIiwic2VyaWVzIiwiY291bnQiLCJnZXRFbGVtZW50QnlJZCIsInRyaWdnZXIiLCJwb3NpdGlvbiIsImF4aXNWYWx1ZSIsIk9iamVjdCIsImtleXMiLCJkYXRhWm9vbSIsImhhbmRsZUljb24iLCJoYW5kbGVTaXplIiwiaGFuZGxlU3R5bGUiLCJzaGFkb3dCbHVyIiwic2hhZG93Q29sb3IiLCJzaGFkb3dPZmZzZXRYIiwic2hhZG93T2Zmc2V0WSIsInZhbHVlcyIsInNtb290aCIsInN5bWJvbCIsInNhbXBsaW5nIiwiYXJlYVN0eWxlIiwibm9ybWFsIiwiZW50cmllcyIsImZvckVhY2giLCJ5ZWFyIiwieWVhckRhdGEiLCJpbmRleCIsInB1c2giLCJ2aXN1YWxNYXAiLCJvcmllbnQiLCJyaWdodCIsImJvdHRvbSIsInBpZWNlcyIsIm1pbiIsIm1heCIsImNhbGVuZGFyIiwic2hvdyIsInRvcCIsImxlZnQiLCJyYW5nZSIsImNlbGxTaXplIiwieWVhckxhYmVsIiwiaXRlbVN0eWxlIiwiYm9yZGVyV2lkdGgiLCJib3JkZXJDb2xvciIsImRheUxhYmVsIiwibW9udGhMYWJlbCIsIm5hbWVNYXAiLCJjb29yZGluYXRlU3lzdGVtIiwiY2FsZW5kYXJJbmRleCIsInRyaWdnZXJPbiIsImxheW91dCIsInN5bWJvbFNpemUiLCJpbml0aWFsVHJlZURlcHRoIiwiYW5pbWF0aW9uRHVyYXRpb25VcGRhdGUiLCJsYWJlbCJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFFQTtDQUdBOztBQUNBQSxtQkFBTyxDQUFDLHdFQUFELENBQVA7O0FBQ0FBLG1CQUFPLENBQUMsc0VBQUQsQ0FBUDs7QUFDQUEsbUJBQU8sQ0FBQyw4RUFBRCxDQUFQOztBQUNBQSxtQkFBTyxDQUFDLHdFQUFELENBQVA7O0FBQ0FBLG1CQUFPLENBQUMsd0ZBQUQsQ0FBUDs7QUFDQUEsbUJBQU8sQ0FBQyxzRkFBRCxDQUFQOztBQUNBQSxtQkFBTyxDQUFDLDBGQUFELENBQVA7O0FBRUEsSUFBSUMsV0FBVyxHQUFHLENBQ2RDLGdFQUFBLENBQWlCLHVCQUFqQixDQURjLEVBRWRBLGdFQUFBLENBQWlCLHdCQUFqQixDQUZjLEVBR2RBLGdFQUFBLENBQWlCLHFCQUFqQixDQUhjLEVBSWRBLGdFQUFBLENBQWlCLHFCQUFqQixDQUpjLEVBS2RBLGdFQUFBLENBQWlCLG1CQUFqQixDQUxjLEVBTWRBLGdFQUFBLENBQWlCLG9CQUFqQixDQU5jLEVBT2RBLGdFQUFBLENBQWlCLG9CQUFqQixDQVBjLEVBUWRBLGdFQUFBLENBQWlCLHNCQUFqQixDQVJjLEVBU2RBLGdFQUFBLENBQWlCLHlCQUFqQixDQVRjLEVBVWRBLGdFQUFBLENBQWlCLHVCQUFq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a0MsVUFBTSxFQUFFLENBQ0o7QUFDSVgsVUFBSSxFQUFFLFNBRFY7QUFFSTJELHNCQUFnQixFQUFFLFVBRnRCO0FBR0lDLG1CQUFhLEVBQUUsQ0FIbkI7QUFJSXJFLFVBQUksRUFBRUE7QUFKVixLQURJO0FBNUN3RCxHQUFwRTtBQXFESCxDQTVERDtBQThEQUMsZ0VBQUEsQ0FBYWpCLFFBQVEsQ0FBQ3NDLGNBQVQsQ0FBd0IsYUFBeEIsQ0FBYixFQUFxRHBCLFNBQXJELENBQStEO0FBQzNEQyxTQUFPLEVBQUU7QUFDTG9CLFdBQU8sRUFBRSxNQURKO0FBRUwrQyxhQUFTLEVBQUU7QUFGTixHQURrRDtBQUszRGxELFFBQU0sRUFBRSxDQUNKO0FBQ0lYLFFBQUksRUFBRSxNQURWO0FBRUlULFFBQUksRUFBRSxDQUFDSCxRQUFELENBRlY7QUFHSTBFLFVBQU0sRUFBRSxRQUhaO0FBSUlqQyxVQUFNLEVBQUUsYUFKWjtBQUtJa0MsY0FBVSxFQUFFLENBTGhCO0FBTUlDLG9CQUFnQixFQUFFLENBQUMsQ0FOdkI7QUFPSUMsMkJBQXVCLEVBQUUsR0FQN0I7QUFRSVosYUFBUyxFQUFFO0FBQ1BFLGlCQUFXLEVBQUU1RTtBQUROLEtBUmY7QUFXSTRCLGFBQVMsRUFBRTtBQUNQVCxXQUFLLEVBQUVyQixVQUFVLEdBQUcsU0FBSCxHQUFlO0FBRHpCLEtBWGY7QUFjSXlGLFNBQUssRUFBRTtBQUNIcEUsV0FBSyxFQUFFckIsVUFBVSxHQUFHLFNBQUgsR0FBZTtBQUQ3QjtBQWRYLEdBREk7QUFMbUQsQ0FBL0QsRSIsImZpbGUiOiJzdGF0aXN0aWNzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0ICcuL3RyYW5zbGF0aW9ucy9jb25maWcnXG5pbXBvcnQgJy4vdHJhbnNsYXRpb25zL2phdmFzY3JpcHQvZW4tR0InXG5pbXBvcnQgJy4vdHJhbnNsYXRpb25zL2phdmFzY3JpcHQvZnItRlInXG5cbmltcG9ydCBUcmFuc2xhdG9yIGZyb20gJy4vdHJhbnNsYXRvci5taW4uanMnXG5pbXBvcnQgZWNoYXJ0cyBmcm9tICdlY2hhcnRzL2xpYi9lY2hhcnRzJ1xuXG4vL0VjaGFydHMgY29tcG9uZW50c1xucmVxdWlyZShcImVjaGFydHMvbGliL2NoYXJ0L2xpbmVcIik7XG5yZXF1aXJlKCdlY2hhcnRzL2xpYi9jaGFydC9iYXInKTtcbnJlcXVpcmUoJ2VjaGFydHMvbGliL2NoYXJ0L2hlYXRtYXAnKTtcbnJlcXVpcmUoJ2VjaGFydHMvbGliL2NoYXJ0L3RyZWUnKTtcbnJlcXVpcmUoJ2VjaGFydHMvbGliL2NvbXBvbmVudC9jYWxlbmRhcicpO1xucmVxdWlyZSgnZWNoYXJ0cy9saWIvY29tcG9uZW50L3Rvb2x0aXAnKTtcbnJlcXVpcmUoJ2VjaGFydHMvbGliL2NvbXBvbmVudC92aXN1YWxNYXAnKTtcblxubGV0IG1vbnRoc0xhYmVsID0gW1xuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMuamFudWFyeScpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMuZmVicnVhcnknKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLm1hcmNoJyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5hcHJpbCcpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMubWF5JyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5qdW5lJyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5qdWx5JyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5hdWd1c3QnKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLnNlcHRlbWJlcicpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMub2N0b2JlcicpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMubm92ZW1iZXInKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLmRlY2VtYmVyJylcbl07XG5cbmxldCBzdGF0aXN0aWNIb2xkZXIgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcuc3RhdGlzdGljcy1ob2xkZXInKTtcbmxldCBpc0RhcmtNb2RlID0gIHN0YXRpc3RpY0hvbGRlci5kYXRhc2V0LmlzRGFya01vZGUgPT0gMSA/IHRydWUgOiBmYWxzZTtcbmxldCB0aGVtZU1haW5IdWUgPSBzdGF0aXN0aWNIb2xkZXIuZGF0YXNldC50aGVtZU1haW5IdWU7XG5sZXQgdGhlbWVEYXJrSHVlID0gc3RhdGlzdGljSG9sZGVyLmRhdGFzZXQudGhlbWVEYXJrSHVlO1xubGV0IHRoZW1lTGlnaHRIdWUgPSBzdGF0aXN0aWNIb2xkZXIuZGF0YXNldC50aGVtZUxpZ2h0SHVlO1xubGV0IHRoZW1lTGlnaHRlc3RIdWUgPSBzdGF0aXN0aWNIb2xkZXIuZGF0YXNldC50aGVtZUxpZ2h0ZXN0SHVlO1xuXG5sZXQgaXRlbXNFdm9sdXRpb25EYXRhID0gSlNPTi5wYXJzZShkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjaXRlbXMtZXZvbHV0aW9uLWNoYXJ0JykuZGF0YXNldC5qc29uKTtcbmxldCBjYWxlbmRhcnNEYXRhID0gSlNPTi5wYXJzZShkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjY2FsZW5kYXJzJykuZGF0YXNldC5qc29uKTtcbmxldCB0cmVlSnNvbiA9IEpTT04ucGFyc2UoZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI3JhZGlhbC10cmVlJykuZGF0YXNldC5qc29uKTtcblxubG9hZENoYXJ0KGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNtb250aC1kYXlzLWNoYXJ0JykpO1xubG9hZENoYXJ0KGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNob3Vycy1jaGFydCcpKTtcbmxvYWRDaGFydChkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjbW9udGhzLWNoYXJ0JykpO1xubG9hZENoYXJ0KGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyN3ZWVrLWRheXMtY2hhcnQnKSk7XG5cbmZ1bmN0aW9uIGxvYWRDaGFydChlbGVtZW50KSB7XG4gICAgY29uc3QgZGF0YSA9IEpTT04ucGFyc2UoZWxlbWVudC5kYXRhc2V0Lmpzb24pO1xuICAgIGVjaGFydHMuaW5pdChlbGVtZW50KS5zZXRPcHRpb24oe1xuICAgICAgICB0b29sdGlwOiB7XG4gICAgICAgICAgICBmb3JtYXR0ZXI6IGZ1bmN0aW9uIChwYXJhbXMpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gVHJhbnNsYXRvci50cmFuc0Nob2ljZSgnc3RhdGlzdGljcy5pdGVtc19hZGRlZCcsIHBhcmFtcy52YWx1ZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGNvbG9yOiBbdGhlbWVNYWluSHVlXSxcbiAgICAgICAgeEF4aXM6IHtcbiAgICAgICAgICAgIHR5cGUgOiAnY2F0ZWdvcnknLFxuICAgICAgICAgICAgZGF0YTogZGF0YS5tYXAoZWxlbWVudCA9PiBlbGVtZW50LmFic2Npc3NhKSxcbiAgICAgICAgICAgIGF4aXNMYWJlbDoge1xuICAgICAgICAgICAgICAgIHRleHRTdHlsZToge1xuICAgICAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGF4aXNUaWNrOiB7XG4gICAgICAgICAgICAgICAgYWxpZ25XaXRoTGFiZWw6IHRydWUsXG4gICAgICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgeUF4aXM6IHtcbiAgICAgICAgICAgIHNwbGl0TGluZToge1xuICAgICAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjN2Q3ZjgyJzogJyNjY2MnXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGF4aXNMYWJlbDoge1xuICAgICAgICAgICAgICAgIHRleHRTdHlsZToge1xuICAgICAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGF4aXNUaWNrOiB7XG4gICAgICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgc2VyaWVzOiBbe1xuICAgICAgICAgICAgdHlwZTogJ2JhcicsXG4gICAgICAgICAgICBkYXRhOiBkYXRhLm1hcChlbGVtZW50ID0+IGVsZW1lbnQuY291bnQpXG4gICAgICAgIH1dXG4gICAgfSk7XG59XG5cbmVjaGFydHMuaW5pdChkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnaXRlbXMtZXZvbHV0aW9uLWNoYXJ0JykpLnNldE9wdGlvbih7XG4gICAgdG9vbHRpcDoge1xuICAgICAgICB0cmlnZ2VyOiAnYXhpcycsXG4gICAgICAgIHBvc2l0aW9uOiBmdW5jdGlvbiAocGFyYW1zKSB7XG4gICAgICAgICAgICByZXR1cm4gW3BhcmFtc1swXSwgJzEwJSddO1xuICAgICAgICB9LFxuICAgICAgICBmb3JtYXR0ZXI6IGZ1bmN0aW9uIChwYXJhbXMpIHtcbiAgICAgICAgICAgIHJldHVybiBwYXJhbXNbMF0uYXhpc1ZhbHVlICsgJzogJyArIFRyYW5zbGF0b3IudHJhbnNDaG9pY2UoJ3N0YXRpc3RpY3MuaXRlbXMnLCBwYXJhbXNbMF0uZGF0YSk7XG4gICAgICAgIH1cbiAgICB9LFxuICAgIGNvbG9yOiBbdGhlbWVNYWluSHVlXSxcbiAgICB4QXhpczoge1xuICAgICAgICB0eXBlOiAnY2F0ZWdvcnknLFxuICAgICAgICBkYXRhOiBPYmplY3Qua2V5cyhpdGVtc0V2b2x1dGlvbkRhdGEpLFxuICAgICAgICBheGlzTGFiZWw6IHtcbiAgICAgICAgICAgIHRleHRTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc1RpY2s6IHtcbiAgICAgICAgICAgIGFsaWduV2l0aExhYmVsOiB0cnVlLFxuICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBheGlzTGluZToge1xuICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfSxcbiAgICB5QXhpczoge1xuICAgICAgICB0eXBlOiAndmFsdWUnLFxuICAgICAgICBheGlzTGFiZWw6IHtcbiAgICAgICAgICAgIHRleHRTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc1RpY2s6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0sXG4gICAgZGF0YVpvb206IFt7XG4gICAgICAgIGhhbmRsZUljb246ICdNMTAuNywxMS45di0xLjNIOS4zdjEuM2MtNC45LDAuMy04LjgsNC40LTguOCw5LjRjMCw1LDMuOSw5LjEsOC44LDkuNHYxLjNoMS4zdi0xLjNjNC45LTAuMyw4LjgtNC40LDguOC05LjRDMTkuNSwxNi4zLDE1LjYsMTIuMiwxMC43LDExLjl6IE0xMy4zLDI0LjRINi43VjIzaDYuNlYyNC40eiBNMTMuMywxOS42SDYuN3YtMS40aDYuNlYxOS42eicsXG4gICAgICAgIGhhbmRsZVNpemU6ICc4MCUnLFxuICAgICAgICBoYW5kbGVTdHlsZToge1xuICAgICAgICAgICAgY29sb3I6ICcjZmZmJyxcbiAgICAgICAgICAgIHNoYWRvd0JsdXI6IDMsXG4gICAgICAgICAgICBzaGFkb3dDb2xvcjogJ3JnYmEoMCwgMCwgMCwgMC42KScsXG4gICAgICAgICAgICBzaGFkb3dPZmZzZXRYOiAyLFxuICAgICAgICAgICAgc2hhZG93T2Zmc2V0WTogMlxuICAgICAgICB9XG4gICAgfV0sXG4gICAgc2VyaWVzOiBbe1xuICAgICAgICBkYXRhOiBPYmplY3QudmFsdWVzKGl0ZW1zRXZvbHV0aW9uRGF0YSksXG4gICAgICAgIHR5cGU6ICdsaW5lJyxcbiAgICAgICAgc21vb3RoOiB0cnVlLFxuICAgICAgICBzeW1ib2w6ICdub25lJyxcbiAgICAgICAgc2FtcGxpbmc6ICdhdmVyYWdlJyxcbiAgICAgICAgYXJlYVN0eWxlOiB7XG4gICAgICAgICAgICBub3JtYWw6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogdGhlbWVNYWluSHVlXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XVxufSk7XG5cblxuXG5PYmplY3QuZW50cmllcyhjYWxlbmRhcnNEYXRhKS5mb3JFYWNoKChbeWVhciwgeWVhckRhdGFdKSA9PiB7XG4gICAgdmFyIGRhdGEgPSBbXTtcblxuICAgIE9iamVjdC5lbnRyaWVzKHllYXJEYXRhKS5mb3JFYWNoKChbaW5kZXgsIHZhbHVlXSkgPT4ge1xuICAgICAgICBkYXRhLnB1c2goW3ZhbHVlWzBdLCBcIlwiICsgdmFsdWVbMV1dKTtcbiAgICB9KTtcblxuICAgIGVjaGFydHMuaW5pdChkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnY2FsZW5kYXJfJyArIHllYXIpKS5zZXRPcHRpb24oe1xuICAgICAgICB0b29sdGlwOiB7XG4gICAgICAgICAgICBmb3JtYXR0ZXI6IGZ1bmN0aW9uIChwYXJhbXMpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gVHJhbnNsYXRvci50cmFuc0Nob2ljZSgnc3RhdGlzdGljcy5pdGVtc19hZGRlZCcsIHBhcmFtcy52YWx1ZVsxXSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIHZpc3VhbE1hcDoge1xuICAgICAgICAgICAgdHlwZTogJ3BpZWNld2lzZScsXG4gICAgICAgICAgICBvcmllbnQ6ICdob3Jpem9udGFsJyxcbiAgICAgICAgICAgIHJpZ2h0OiAnMjE1JyxcbiAgICAgICAgICAgIGJvdHRvbTogJ2JvdHRvbScsXG4gICAgICAgICAgICBwaWVjZXM6IFtcbiAgICAgICAgICAgICAgICB7bWluOiAzMSwgY29sb3I6IHRoZW1lRGFya0h1ZX0sXG4gICAgICAgICAgICAgICAge21pbjogMTYsIG1heDogMzAsIGNvbG9yOiB0aGVtZU1haW5IdWV9LFxuICAgICAgICAgICAgICAgIHttaW46IDYsIG1heDogMTUsIGNvbG9yOiB0aGVtZUxpZ2h0SHVlfSxcbiAgICAgICAgICAgICAgICB7bWluOiAxLCBtYXg6IDUsIGNvbG9yOiB0aGVtZUxpZ2h0ZXN0SHVlfSxcbiAgICAgICAgICAgICAgICB7bWluOiAwLCBtYXg6IDAsIGNvbG9yOiAnI2VkZWRlZCd9XG4gICAgICAgICAgICBdLFxuICAgICAgICB9LFxuICAgICAgICBjYWxlbmRhcjoge1xuICAgICAgICAgICAgc3BsaXRMaW5lOiB7XG4gICAgICAgICAgICAgICAgc2hvdzogZmFsc2UsXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgdG9wOiAnbWlkZGxlJyxcbiAgICAgICAgICAgIGxlZnQ6ICdjZW50ZXInLFxuICAgICAgICAgICAgcmFuZ2U6IHllYXIsXG4gICAgICAgICAgICBjZWxsU2l6ZTogMjAsXG4gICAgICAgICAgICB5ZWFyTGFiZWw6IHtzaG93OiBmYWxzZX0sXG4gICAgICAgICAgICBpdGVtU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBub3JtYWw6IHtcbiAgICAgICAgICAgICAgICAgICAgYm9yZGVyV2lkdGg6IDIsXG4gICAgICAgICAgICAgICAgICAgIGJvcmRlckNvbG9yOiBpc0RhcmtNb2RlID8gJyMzNjM5M2UnIDogJyNmZmZmZmYnLFxuICAgICAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjN2Q3ZjgyJyA6ICcjZWRlZGVkJ1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgZGF5TGFiZWw6IHtcbiAgICAgICAgICAgICAgICBzaG93OiBmYWxzZVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG1vbnRoTGFiZWw6IHtcbiAgICAgICAgICAgICAgICBzaG93OiB0cnVlLFxuICAgICAgICAgICAgICAgIG5hbWVNYXA6IG1vbnRoc0xhYmVsLFxuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgc2VyaWVzOiBbXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdHlwZTogJ2hlYXRtYXAnLFxuICAgICAgICAgICAgICAgIGNvb3JkaW5hdGVTeXN0ZW06ICdjYWxlbmRhcicsXG4gICAgICAgICAgICAgICAgY2FsZW5kYXJJbmRleDogMCxcbiAgICAgICAgICAgICAgICBkYXRhOiBkYXRhXG4gICAgICAgICAgICB9XG4gICAgICAgIF1cbiAgICB9KTtcbn0pO1xuXG5lY2hhcnRzLmluaXQoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3JhZGlhbC10cmVlJykpLnNldE9wdGlvbih7XG4gICAgdG9vbHRpcDoge1xuICAgICAgICB0cmlnZ2VyOiAnaXRlbScsXG4gICAgICAgIHRyaWdnZXJPbjogJ21vdXNlbW92ZSdcbiAgICB9LFxuICAgIHNlcmllczogW1xuICAgICAgICB7XG4gICAgICAgICAgICB0eXBlOiAndHJlZScsXG4gICAgICAgICAgICBkYXRhOiBbdHJlZUpzb25dLFxuICAgICAgICAgICAgbGF5b3V0OiAncmFkaWFsJyxcbiAgICAgICAgICAgIHN5bWJvbDogJ2VtcHR5Q2lyY2xlJyxcbiAgICAgICAgICAgIHN5bWJvbFNpemU6IDcsXG4gICAgICAgICAgICBpbml0aWFsVHJlZURlcHRoOiAtMSxcbiAgICAgICAgICAgIGFuaW1hdGlvbkR1cmF0aW9uVXBkYXRlOiA3NTAsXG4gICAgICAgICAgICBpdGVtU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBib3JkZXJDb2xvcjogdGhlbWVNYWluSHVlLFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyM0YTRiNGQnIDogJyNjY2MnXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgbGFiZWw6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjYTZhN2E4JyA6ICcjNTU1J1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgXVxufSk7XG5cbiJdLCJzb3VyY2VSb290IjoiIn0=