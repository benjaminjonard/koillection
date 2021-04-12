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

var statisticHolder = document.querySelector('.statistics-holder');
var isDarkMode = statisticHolder.dataset.isDarkMode == 1 ? true : false;
var themeMainHue = statisticHolder.dataset.themeMainHue;
var themeDarkHue = statisticHolder.dataset.themeDarkHue;
var themeLightHue = statisticHolder.dataset.themeLightHue;
var themeLightestHue = statisticHolder.dataset.themeLightestHue;
var monthDaysChartData = JSON.parse(document.querySelector('#month-days-chart').dataset.json);
var hoursChartData = JSON.parse(document.querySelector('#hours-chart').dataset.json);
var monthsChartData = JSON.parse(document.querySelector('#months-chart').dataset.json);
var weekDaysChartData = JSON.parse(document.querySelector('#week-days-chart').dataset.json);
var itemsEvolutionData = JSON.parse(document.querySelector('#items-evolution-chart').dataset.json);
var calendarsData = JSON.parse(document.querySelector('#calendars').dataset.json);
var treeJson = JSON.parse(document.querySelector('#radial-tree').dataset.json); // specify chart configuration item and data

echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21___default().init(document.getElementById('month-days-chart')).setOption({
  tooltip: {
    formatter: function formatter(params) {
      return _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().transChoice('statistics.items_added', params.value);
    }
  },
  color: [themeMainHue],
  xAxis: {
    type: 'category',
    data: monthDaysChartData.map(function (element) {
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
    data: monthDaysChartData.map(function (element) {
      return element.count;
    })
  }]
});
echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21___default().init(document.getElementById('hours-chart')).setOption({
  tooltip: {
    formatter: function formatter(params) {
      return _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().transChoice('statistics.items_added', params.value);
    }
  },
  color: [themeMainHue],
  xAxis: {
    type: 'category',
    data: hoursChartData.map(function (element) {
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
    data: hoursChartData.map(function (element) {
      return element.count;
    })
  }]
});
echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21___default().init(document.getElementById('months-chart')).setOption({
  tooltip: {
    formatter: function formatter(params) {
      return _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().transChoice('statistics.items_added', params.value);
    }
  },
  color: [themeMainHue],
  xAxis: {
    type: 'category',
    data: monthsChartData.map(function (element) {
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
    data: monthsChartData.map(function (element) {
      return element.count;
    })
  }]
});
echarts_lib_echarts__WEBPACK_IMPORTED_MODULE_21___default().init(document.getElementById('week-days-chart')).setOption({
  tooltip: {
    formatter: function formatter(params) {
      return _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().transChoice('statistics.items_added', params.value);
    }
  },
  color: [themeMainHue],
  xAxis: {
    type: 'category',
    data: weekDaysChartData.map(function (element) {
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
  splitLine: {
    lineStyle: {
      color: isDarkMode ? '#7d7f82' : '#ccc'
    }
  },
  yAxis: {
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
    data: weekDaysChartData.map(function (element) {
      return element.count;
    })
  }]
});
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
var monthsLabel = [_translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.january'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.february'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.march'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.april'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.may'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.june'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.july'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.august'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.september'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.october'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.november'), _translator_min_js__WEBPACK_IMPORTED_MODULE_20___default().trans('global.months.december')];
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9rb2lsbGVjdGlvbi8uL2pzL3N0YXRpc3RpY3MuanMiXSwibmFtZXMiOlsicmVxdWlyZSIsInN0YXRpc3RpY0hvbGRlciIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsImlzRGFya01vZGUiLCJkYXRhc2V0IiwidGhlbWVNYWluSHVlIiwidGhlbWVEYXJrSHVlIiwidGhlbWVMaWdodEh1ZSIsInRoZW1lTGlnaHRlc3RIdWUiLCJtb250aERheXNDaGFydERhdGEiLCJKU09OIiwicGFyc2UiLCJqc29uIiwiaG91cnNDaGFydERhdGEiLCJtb250aHNDaGFydERhdGEiLCJ3ZWVrRGF5c0NoYXJ0RGF0YSIsIml0ZW1zRXZvbHV0aW9uRGF0YSIsImNhbGVuZGFyc0RhdGEiLCJ0cmVlSnNvbiIsImVjaGFydHMiLCJnZXRFbGVtZW50QnlJZCIsInNldE9wdGlvbiIsInRvb2x0aXAiLCJmb3JtYXR0ZXIiLCJwYXJhbXMiLCJUcmFuc2xhdG9yIiwidmFsdWUiLCJjb2xvciIsInhBeGlzIiwidHlwZSIsImRhdGEiLCJtYXAiLCJlbGVtZW50IiwiYWJzY2lzc2EiLCJheGlzTGFiZWwiLCJ0ZXh0U3R5bGUiLCJheGlzVGljayIsImFsaWduV2l0aExhYmVsIiwibGluZVN0eWxlIiwiYXhpc0xpbmUiLCJ5QXhpcyIsInNwbGl0TGluZSIsInNlcmllcyIsImNvdW50IiwidHJpZ2dlciIsInBvc2l0aW9uIiwiYXhpc1ZhbHVlIiwiT2JqZWN0Iiwia2V5cyIsImRhdGFab29tIiwiaGFuZGxlSWNvbiIsImhhbmRsZVNpemUiLCJoYW5kbGVTdHlsZSIsInNoYWRvd0JsdXIiLCJzaGFkb3dDb2xvciIsInNoYWRvd09mZnNldFgiLCJzaGFkb3dPZmZzZXRZIiwidmFsdWVzIiwic21vb3RoIiwic3ltYm9sIiwic2FtcGxpbmciLCJhcmVhU3R5bGUiLCJub3JtYWwiLCJtb250aHNMYWJlbCIsImVudHJpZXMiLCJmb3JFYWNoIiwieWVhciIsInllYXJEYXRhIiwiaW5kZXgiLCJwdXNoIiwidmlzdWFsTWFwIiwib3JpZW50IiwicmlnaHQiLCJib3R0b20iLCJwaWVjZXMiLCJtaW4iLCJtYXgiLCJjYWxlbmRhciIsInNob3ciLCJ0b3AiLCJsZWZ0IiwicmFuZ2UiLCJjZWxsU2l6ZSIsInllYXJMYWJlbCIsIml0ZW1TdHlsZSIsImJvcmRlcldpZHRoIiwiYm9yZGVyQ29sb3IiLCJkYXlMYWJlbCIsIm1vbnRoTGFiZWwiLCJuYW1lTWFwIiwiY29vcmRpbmF0ZVN5c3RlbSIsImNhbGVuZGFySW5kZXgiLCJ0cmlnZ2VyT24iLCJsYXlvdXQiLCJzeW1ib2xTaXplIiwiaW5pdGlhbFRyZWVEZXB0aCIsImFuaW1hdGlvbkR1cmF0aW9uVXBkYXRlIiwibGFiZWwiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBRUE7Q0FHQTs7QUFDQUEsbUJBQU8sQ0FBQyx3RUFBRCxDQUFQOztBQUNBQSxtQkFBTyxDQUFDLHNFQUFELENBQVA7O0FBQ0FBLG1CQUFPLENBQUMsOEVBQUQsQ0FBUDs7QUFDQUEsbUJBQU8sQ0FBQyx3RUFBRCxDQUFQOztBQUNBQSxtQkFBTyxDQUFDLHdGQUFELENBQVA7O0FBQ0FBLG1CQUFPLENBQUMsc0ZBQUQsQ0FBUDs7QUFDQUEsbUJBQU8sQ0FBQywwRkFBRCxDQUFQOztBQUVBLElBQUlDLGVBQWUsR0FBR0MsUUFBUSxDQUFDQyxhQUFULENBQXVCLG9CQUF2QixDQUF0QjtBQUNBLElBQUlDLFVBQVUsR0FBSUgsZUFBZSxDQUFDSSxPQUFoQixDQUF3QkQsVUFBeEIsSUFBc0MsQ0FBdEMsR0FBMEMsSUFBMUMsR0FBaUQsS0FBbkU7QUFDQSxJQUFJRSxZQUFZLEdBQUdMLGVBQWUsQ0FBQ0ksT0FBaEIsQ0FBd0JDLFlBQTNDO0FBQ0EsSUFBSUMsWUFBWSxHQUFHTixlQUFlLENBQUNJLE9BQWhCLENBQXdCRSxZQUEzQztB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L2NvbmZpZydcbmltcG9ydCAnLi90cmFuc2xhdGlvbnMvamF2YXNjcmlwdC9lbi1HQidcbmltcG9ydCAnLi90cmFuc2xhdGlvbnMvamF2YXNjcmlwdC9mci1GUidcblxuaW1wb3J0IFRyYW5zbGF0b3IgZnJvbSAnLi90cmFuc2xhdG9yLm1pbi5qcydcbmltcG9ydCBlY2hhcnRzIGZyb20gJ2VjaGFydHMvbGliL2VjaGFydHMnXG5cbi8vRWNoYXJ0cyBjb21wb25lbnRzXG5yZXF1aXJlKFwiZWNoYXJ0cy9saWIvY2hhcnQvbGluZVwiKTtcbnJlcXVpcmUoJ2VjaGFydHMvbGliL2NoYXJ0L2JhcicpO1xucmVxdWlyZSgnZWNoYXJ0cy9saWIvY2hhcnQvaGVhdG1hcCcpO1xucmVxdWlyZSgnZWNoYXJ0cy9saWIvY2hhcnQvdHJlZScpO1xucmVxdWlyZSgnZWNoYXJ0cy9saWIvY29tcG9uZW50L2NhbGVuZGFyJyk7XG5yZXF1aXJlKCdlY2hhcnRzL2xpYi9jb21wb25lbnQvdG9vbHRpcCcpO1xucmVxdWlyZSgnZWNoYXJ0cy9saWIvY29tcG9uZW50L3Zpc3VhbE1hcCcpO1xuXG5sZXQgc3RhdGlzdGljSG9sZGVyID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignLnN0YXRpc3RpY3MtaG9sZGVyJyk7XG5sZXQgaXNEYXJrTW9kZSA9ICBzdGF0aXN0aWNIb2xkZXIuZGF0YXNldC5pc0RhcmtNb2RlID09IDEgPyB0cnVlIDogZmFsc2U7XG5sZXQgdGhlbWVNYWluSHVlID0gc3RhdGlzdGljSG9sZGVyLmRhdGFzZXQudGhlbWVNYWluSHVlO1xubGV0IHRoZW1lRGFya0h1ZSA9IHN0YXRpc3RpY0hvbGRlci5kYXRhc2V0LnRoZW1lRGFya0h1ZTtcbmxldCB0aGVtZUxpZ2h0SHVlID0gc3RhdGlzdGljSG9sZGVyLmRhdGFzZXQudGhlbWVMaWdodEh1ZTtcbmxldCB0aGVtZUxpZ2h0ZXN0SHVlID0gc3RhdGlzdGljSG9sZGVyLmRhdGFzZXQudGhlbWVMaWdodGVzdEh1ZTtcbmxldCBtb250aERheXNDaGFydERhdGEgPSBKU09OLnBhcnNlKGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNtb250aC1kYXlzLWNoYXJ0JykuZGF0YXNldC5qc29uKTtcbmxldCBob3Vyc0NoYXJ0RGF0YSA9IEpTT04ucGFyc2UoZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2hvdXJzLWNoYXJ0JykuZGF0YXNldC5qc29uKTtcbmxldCBtb250aHNDaGFydERhdGEgPSBKU09OLnBhcnNlKGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNtb250aHMtY2hhcnQnKS5kYXRhc2V0Lmpzb24pO1xubGV0IHdlZWtEYXlzQ2hhcnREYXRhID0gSlNPTi5wYXJzZShkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjd2Vlay1kYXlzLWNoYXJ0JykuZGF0YXNldC5qc29uKTtcbmxldCBpdGVtc0V2b2x1dGlvbkRhdGEgPSBKU09OLnBhcnNlKGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNpdGVtcy1ldm9sdXRpb24tY2hhcnQnKS5kYXRhc2V0Lmpzb24pO1xubGV0IGNhbGVuZGFyc0RhdGEgPSBKU09OLnBhcnNlKGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNjYWxlbmRhcnMnKS5kYXRhc2V0Lmpzb24pO1xubGV0IHRyZWVKc29uID0gSlNPTi5wYXJzZShkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjcmFkaWFsLXRyZWUnKS5kYXRhc2V0Lmpzb24pO1xuXG4vLyBzcGVjaWZ5IGNoYXJ0IGNvbmZpZ3VyYXRpb24gaXRlbSBhbmQgZGF0YVxuZWNoYXJ0cy5pbml0KGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtb250aC1kYXlzLWNoYXJ0JykpLnNldE9wdGlvbih7XG4gICAgdG9vbHRpcDoge1xuICAgICAgICBmb3JtYXR0ZXI6IGZ1bmN0aW9uIChwYXJhbXMpIHtcbiAgICAgICAgICAgIHJldHVybiBUcmFuc2xhdG9yLnRyYW5zQ2hvaWNlKCdzdGF0aXN0aWNzLml0ZW1zX2FkZGVkJywgcGFyYW1zLnZhbHVlKTtcbiAgICAgICAgfVxuICAgIH0sXG4gICAgY29sb3I6IFt0aGVtZU1haW5IdWVdLFxuICAgIHhBeGlzOiB7XG4gICAgICAgIHR5cGUgOiAnY2F0ZWdvcnknLFxuICAgICAgICBkYXRhOiBtb250aERheXNDaGFydERhdGEubWFwKGVsZW1lbnQgPT4gZWxlbWVudC5hYnNjaXNzYSksXG4gICAgICAgIGF4aXNMYWJlbDoge1xuICAgICAgICAgICAgdGV4dFN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBheGlzVGljazoge1xuICAgICAgICAgICAgYWxpZ25XaXRoTGFiZWw6IHRydWUsXG4gICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGF4aXNMaW5lOiB7XG4gICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9LFxuICAgIHlBeGlzOiB7XG4gICAgICAgIHNwbGl0TGluZToge1xuICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnIzdkN2Y4Mic6ICcjY2NjJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBheGlzTGFiZWw6IHtcbiAgICAgICAgICAgIHRleHRTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc1RpY2s6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0sXG4gICAgc2VyaWVzOiBbe1xuICAgICAgICB0eXBlOiAnYmFyJyxcbiAgICAgICAgZGF0YTogbW9udGhEYXlzQ2hhcnREYXRhLm1hcChlbGVtZW50ID0+IGVsZW1lbnQuY291bnQpXG4gICAgfV1cbn0pO1xuXG5lY2hhcnRzLmluaXQoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2hvdXJzLWNoYXJ0JykpLnNldE9wdGlvbih7XG4gICAgdG9vbHRpcDoge1xuICAgICAgICBmb3JtYXR0ZXI6IGZ1bmN0aW9uIChwYXJhbXMpIHtcbiAgICAgICAgICAgIHJldHVybiBUcmFuc2xhdG9yLnRyYW5zQ2hvaWNlKCdzdGF0aXN0aWNzLml0ZW1zX2FkZGVkJywgcGFyYW1zLnZhbHVlKTtcbiAgICAgICAgfVxuICAgIH0sXG4gICAgY29sb3I6IFt0aGVtZU1haW5IdWVdLFxuICAgIHhBeGlzOiB7XG4gICAgICAgIHR5cGUgOiAnY2F0ZWdvcnknLFxuICAgICAgICBkYXRhOiBob3Vyc0NoYXJ0RGF0YS5tYXAoZWxlbWVudCA9PiBlbGVtZW50LmFic2Npc3NhKSxcbiAgICAgICAgYXhpc0xhYmVsOiB7XG4gICAgICAgICAgICB0ZXh0U3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGF4aXNUaWNrOiB7XG4gICAgICAgICAgICBhbGlnbldpdGhMYWJlbDogdHJ1ZSxcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0sXG4gICAgeUF4aXM6IHtcbiAgICAgICAgc3BsaXRMaW5lOiB7XG4gICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjN2Q3ZjgyJzogJyNjY2MnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGF4aXNMYWJlbDoge1xuICAgICAgICAgICAgdGV4dFN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBheGlzVGljazoge1xuICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBheGlzTGluZToge1xuICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfSxcbiAgICBzZXJpZXM6IFt7XG4gICAgICAgIHR5cGU6ICdiYXInLFxuICAgICAgICBkYXRhOiBob3Vyc0NoYXJ0RGF0YS5tYXAoZWxlbWVudCA9PiBlbGVtZW50LmNvdW50KVxuICAgIH1dXG59KTtcblxuZWNoYXJ0cy5pbml0KGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtb250aHMtY2hhcnQnKSkuc2V0T3B0aW9uKHtcbiAgICB0b29sdGlwOiB7XG4gICAgICAgIGZvcm1hdHRlcjogZnVuY3Rpb24gKHBhcmFtcykge1xuICAgICAgICAgICAgcmV0dXJuIFRyYW5zbGF0b3IudHJhbnNDaG9pY2UoJ3N0YXRpc3RpY3MuaXRlbXNfYWRkZWQnLCBwYXJhbXMudmFsdWUpO1xuICAgICAgICB9XG4gICAgfSxcbiAgICBjb2xvcjogW3RoZW1lTWFpbkh1ZV0sXG4gICAgeEF4aXM6IHtcbiAgICAgICAgdHlwZSA6ICdjYXRlZ29yeScsXG4gICAgICAgIGRhdGE6IG1vbnRoc0NoYXJ0RGF0YS5tYXAoZWxlbWVudCA9PiBlbGVtZW50LmFic2Npc3NhKSxcbiAgICAgICAgYXhpc0xhYmVsOiB7XG4gICAgICAgICAgICB0ZXh0U3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGF4aXNUaWNrOiB7XG4gICAgICAgICAgICBhbGlnbldpdGhMYWJlbDogdHJ1ZSxcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0sXG4gICAgeUF4aXM6IHtcbiAgICAgICAgc3BsaXRMaW5lOiB7XG4gICAgICAgICAgICBsaW5lU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjN2Q3ZjgyJzogJyNjY2MnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGF4aXNMYWJlbDoge1xuICAgICAgICAgICAgdGV4dFN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBheGlzVGljazoge1xuICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBheGlzTGluZToge1xuICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfSxcbiAgICBzZXJpZXM6IFt7XG4gICAgICAgIHR5cGU6ICdiYXInLFxuICAgICAgICBkYXRhOiBtb250aHNDaGFydERhdGEubWFwKGVsZW1lbnQgPT4gZWxlbWVudC5jb3VudClcbiAgICB9XVxufSk7XG5cbmVjaGFydHMuaW5pdChkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnd2Vlay1kYXlzLWNoYXJ0JykpLnNldE9wdGlvbih7XG4gICAgdG9vbHRpcDoge1xuICAgICAgICBmb3JtYXR0ZXI6IGZ1bmN0aW9uIChwYXJhbXMpIHtcbiAgICAgICAgICAgIHJldHVybiBUcmFuc2xhdG9yLnRyYW5zQ2hvaWNlKCdzdGF0aXN0aWNzLml0ZW1zX2FkZGVkJywgcGFyYW1zLnZhbHVlKTtcbiAgICAgICAgfVxuICAgIH0sXG4gICAgY29sb3I6IFt0aGVtZU1haW5IdWVdLFxuICAgIHhBeGlzOiB7XG4gICAgICAgIHR5cGUgOiAnY2F0ZWdvcnknLFxuICAgICAgICBkYXRhOiB3ZWVrRGF5c0NoYXJ0RGF0YS5tYXAoZWxlbWVudCA9PiBlbGVtZW50LmFic2Npc3NhKSxcbiAgICAgICAgYXhpc0xhYmVsOiB7XG4gICAgICAgICAgICB0ZXh0U3R5bGU6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjZjBmMGYwJzogJyMzMjMyMzMnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIGF4aXNUaWNrOiB7XG4gICAgICAgICAgICBhbGlnbldpdGhMYWJlbDogdHJ1ZSxcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0sXG4gICAgc3BsaXRMaW5lOiB7XG4gICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnIzdkN2Y4Mic6ICcjY2NjJ1xuICAgICAgICB9XG4gICAgfSxcbiAgICB5QXhpczoge1xuICAgICAgICBheGlzTGFiZWw6IHtcbiAgICAgICAgICAgIHRleHRTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc1RpY2s6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0sXG4gICAgc2VyaWVzOiBbe1xuICAgICAgICB0eXBlOiAnYmFyJyxcbiAgICAgICAgZGF0YTogd2Vla0RheXNDaGFydERhdGEubWFwKGVsZW1lbnQgPT4gZWxlbWVudC5jb3VudClcbiAgICB9XVxufSk7XG5cbmVjaGFydHMuaW5pdChkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnaXRlbXMtZXZvbHV0aW9uLWNoYXJ0JykpLnNldE9wdGlvbih7XG4gICAgdG9vbHRpcDoge1xuICAgICAgICB0cmlnZ2VyOiAnYXhpcycsXG4gICAgICAgIHBvc2l0aW9uOiBmdW5jdGlvbiAocGFyYW1zKSB7XG4gICAgICAgICAgICByZXR1cm4gW3BhcmFtc1swXSwgJzEwJSddO1xuICAgICAgICB9LFxuICAgICAgICBmb3JtYXR0ZXI6IGZ1bmN0aW9uIChwYXJhbXMpIHtcbiAgICAgICAgICAgIHJldHVybiBwYXJhbXNbMF0uYXhpc1ZhbHVlICsgJzogJyArIFRyYW5zbGF0b3IudHJhbnNDaG9pY2UoJ3N0YXRpc3RpY3MuaXRlbXMnLCBwYXJhbXNbMF0uZGF0YSk7XG4gICAgICAgIH1cbiAgICB9LFxuICAgIGNvbG9yOiBbdGhlbWVNYWluSHVlXSxcbiAgICB4QXhpczoge1xuICAgICAgICB0eXBlOiAnY2F0ZWdvcnknLFxuICAgICAgICBkYXRhOiBPYmplY3Qua2V5cyhpdGVtc0V2b2x1dGlvbkRhdGEpLFxuICAgICAgICBheGlzTGFiZWw6IHtcbiAgICAgICAgICAgIHRleHRTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc1RpY2s6IHtcbiAgICAgICAgICAgIGFsaWduV2l0aExhYmVsOiB0cnVlLFxuICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBheGlzTGluZToge1xuICAgICAgICAgICAgbGluZVN0eWxlOiB7XG4gICAgICAgICAgICAgICAgY29sb3I6IGlzRGFya01vZGUgPyAnI2YwZjBmMCc6ICcjMzIzMjMzJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfSxcbiAgICB5QXhpczoge1xuICAgICAgICB0eXBlOiAndmFsdWUnLFxuICAgICAgICBheGlzTGFiZWw6IHtcbiAgICAgICAgICAgIHRleHRTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc1RpY2s6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgYXhpc0xpbmU6IHtcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0sXG4gICAgZGF0YVpvb206IFt7XG4gICAgICAgIGhhbmRsZUljb246ICdNMTAuNywxMS45di0xLjNIOS4zdjEuM2MtNC45LDAuMy04LjgsNC40LTguOCw5LjRjMCw1LDMuOSw5LjEsOC44LDkuNHYxLjNoMS4zdi0xLjNjNC45LTAuMyw4LjgtNC40LDguOC05LjRDMTkuNSwxNi4zLDE1LjYsMTIuMiwxMC43LDExLjl6IE0xMy4zLDI0LjRINi43VjIzaDYuNlYyNC40eiBNMTMuMywxOS42SDYuN3YtMS40aDYuNlYxOS42eicsXG4gICAgICAgIGhhbmRsZVNpemU6ICc4MCUnLFxuICAgICAgICBoYW5kbGVTdHlsZToge1xuICAgICAgICAgICAgY29sb3I6ICcjZmZmJyxcbiAgICAgICAgICAgIHNoYWRvd0JsdXI6IDMsXG4gICAgICAgICAgICBzaGFkb3dDb2xvcjogJ3JnYmEoMCwgMCwgMCwgMC42KScsXG4gICAgICAgICAgICBzaGFkb3dPZmZzZXRYOiAyLFxuICAgICAgICAgICAgc2hhZG93T2Zmc2V0WTogMlxuICAgICAgICB9XG4gICAgfV0sXG4gICAgc2VyaWVzOiBbe1xuICAgICAgICBkYXRhOiBPYmplY3QudmFsdWVzKGl0ZW1zRXZvbHV0aW9uRGF0YSksXG4gICAgICAgIHR5cGU6ICdsaW5lJyxcbiAgICAgICAgc21vb3RoOiB0cnVlLFxuICAgICAgICBzeW1ib2w6ICdub25lJyxcbiAgICAgICAgc2FtcGxpbmc6ICdhdmVyYWdlJyxcbiAgICAgICAgYXJlYVN0eWxlOiB7XG4gICAgICAgICAgICBub3JtYWw6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogdGhlbWVNYWluSHVlXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XVxufSk7XG5cbmxldCBtb250aHNMYWJlbCA9IFtcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLmphbnVhcnknKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLmZlYnJ1YXJ5JyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5tYXJjaCcpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMuYXByaWwnKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLm1heScpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMuanVuZScpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMuanVseScpLFxuICAgIFRyYW5zbGF0b3IudHJhbnMoJ2dsb2JhbC5tb250aHMuYXVndXN0JyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5zZXB0ZW1iZXInKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLm9jdG9iZXInKSxcbiAgICBUcmFuc2xhdG9yLnRyYW5zKCdnbG9iYWwubW9udGhzLm5vdmVtYmVyJyksXG4gICAgVHJhbnNsYXRvci50cmFucygnZ2xvYmFsLm1vbnRocy5kZWNlbWJlcicpXG5dO1xuXG5PYmplY3QuZW50cmllcyhjYWxlbmRhcnNEYXRhKS5mb3JFYWNoKChbeWVhciwgeWVhckRhdGFdKSA9PiB7XG4gICAgdmFyIGRhdGEgPSBbXTtcblxuICAgIE9iamVjdC5lbnRyaWVzKHllYXJEYXRhKS5mb3JFYWNoKChbaW5kZXgsIHZhbHVlXSkgPT4ge1xuICAgICAgICBkYXRhLnB1c2goW3ZhbHVlWzBdLCBcIlwiICsgdmFsdWVbMV1dKTtcbiAgICB9KTtcblxuICAgIGVjaGFydHMuaW5pdChkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnY2FsZW5kYXJfJyArIHllYXIpKS5zZXRPcHRpb24oe1xuICAgICAgICB0b29sdGlwOiB7XG4gICAgICAgICAgICBmb3JtYXR0ZXI6IGZ1bmN0aW9uIChwYXJhbXMpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gVHJhbnNsYXRvci50cmFuc0Nob2ljZSgnc3RhdGlzdGljcy5pdGVtc19hZGRlZCcsIHBhcmFtcy52YWx1ZVsxXSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIHZpc3VhbE1hcDoge1xuICAgICAgICAgICAgdHlwZTogJ3BpZWNld2lzZScsXG4gICAgICAgICAgICBvcmllbnQ6ICdob3Jpem9udGFsJyxcbiAgICAgICAgICAgIHJpZ2h0OiAnMjE1JyxcbiAgICAgICAgICAgIGJvdHRvbTogJ2JvdHRvbScsXG4gICAgICAgICAgICBwaWVjZXM6IFtcbiAgICAgICAgICAgICAgICB7bWluOiAzMSwgY29sb3I6IHRoZW1lRGFya0h1ZX0sXG4gICAgICAgICAgICAgICAge21pbjogMTYsIG1heDogMzAsIGNvbG9yOiB0aGVtZU1haW5IdWV9LFxuICAgICAgICAgICAgICAgIHttaW46IDYsIG1heDogMTUsIGNvbG9yOiB0aGVtZUxpZ2h0SHVlfSxcbiAgICAgICAgICAgICAgICB7bWluOiAxLCBtYXg6IDUsIGNvbG9yOiB0aGVtZUxpZ2h0ZXN0SHVlfSxcbiAgICAgICAgICAgICAgICB7bWluOiAwLCBtYXg6IDAsIGNvbG9yOiAnI2VkZWRlZCd9XG4gICAgICAgICAgICBdLFxuICAgICAgICB9LFxuICAgICAgICBjYWxlbmRhcjoge1xuICAgICAgICAgICAgc3BsaXRMaW5lOiB7XG4gICAgICAgICAgICAgICAgc2hvdzogZmFsc2UsXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgdG9wOiAnbWlkZGxlJyxcbiAgICAgICAgICAgIGxlZnQ6ICdjZW50ZXInLFxuICAgICAgICAgICAgcmFuZ2U6IHllYXIsXG4gICAgICAgICAgICBjZWxsU2l6ZTogMjAsXG4gICAgICAgICAgICB5ZWFyTGFiZWw6IHtzaG93OiBmYWxzZX0sXG4gICAgICAgICAgICBpdGVtU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBub3JtYWw6IHtcbiAgICAgICAgICAgICAgICAgICAgYm9yZGVyV2lkdGg6IDIsXG4gICAgICAgICAgICAgICAgICAgIGJvcmRlckNvbG9yOiBpc0RhcmtNb2RlID8gJyMzNjM5M2UnIDogJyNmZmZmZmYnLFxuICAgICAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjN2Q3ZjgyJyA6ICcjZWRlZGVkJ1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgZGF5TGFiZWw6IHtcbiAgICAgICAgICAgICAgICBzaG93OiBmYWxzZVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG1vbnRoTGFiZWw6IHtcbiAgICAgICAgICAgICAgICBzaG93OiB0cnVlLFxuICAgICAgICAgICAgICAgIG5hbWVNYXA6IG1vbnRoc0xhYmVsLFxuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyNmMGYwZjAnOiAnIzMyMzIzMydcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgc2VyaWVzOiBbXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdHlwZTogJ2hlYXRtYXAnLFxuICAgICAgICAgICAgICAgIGNvb3JkaW5hdGVTeXN0ZW06ICdjYWxlbmRhcicsXG4gICAgICAgICAgICAgICAgY2FsZW5kYXJJbmRleDogMCxcbiAgICAgICAgICAgICAgICBkYXRhOiBkYXRhXG4gICAgICAgICAgICB9XG4gICAgICAgIF1cbiAgICB9KTtcbn0pO1xuXG5lY2hhcnRzLmluaXQoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3JhZGlhbC10cmVlJykpLnNldE9wdGlvbih7XG4gICAgdG9vbHRpcDoge1xuICAgICAgICB0cmlnZ2VyOiAnaXRlbScsXG4gICAgICAgIHRyaWdnZXJPbjogJ21vdXNlbW92ZSdcbiAgICB9LFxuICAgIHNlcmllczogW1xuICAgICAgICB7XG4gICAgICAgICAgICB0eXBlOiAndHJlZScsXG4gICAgICAgICAgICBkYXRhOiBbdHJlZUpzb25dLFxuICAgICAgICAgICAgbGF5b3V0OiAncmFkaWFsJyxcbiAgICAgICAgICAgIHN5bWJvbDogJ2VtcHR5Q2lyY2xlJyxcbiAgICAgICAgICAgIHN5bWJvbFNpemU6IDcsXG4gICAgICAgICAgICBpbml0aWFsVHJlZURlcHRoOiAtMSxcbiAgICAgICAgICAgIGFuaW1hdGlvbkR1cmF0aW9uVXBkYXRlOiA3NTAsXG4gICAgICAgICAgICBpdGVtU3R5bGU6IHtcbiAgICAgICAgICAgICAgICBib3JkZXJDb2xvcjogdGhlbWVNYWluSHVlLFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGxpbmVTdHlsZToge1xuICAgICAgICAgICAgICAgIGNvbG9yOiBpc0RhcmtNb2RlID8gJyM0YTRiNGQnIDogJyNjY2MnXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgbGFiZWw6IHtcbiAgICAgICAgICAgICAgICBjb2xvcjogaXNEYXJrTW9kZSA/ICcjYTZhN2E4JyA6ICcjNTU1J1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgXVxufSk7XG5cbiJdLCJzb3VyY2VSb290IjoiIn0=