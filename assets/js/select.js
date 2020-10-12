import Translator from "./translator.min";

export function loadSelect2() { $('select:not(.js-select-country, .js-select-locale, .js-select-tag-category)').select2(getDefaultConf()); }
export function loadSelect2Countries() { $('select.js-select-country').select2(getCountriesConf()); }
export function loadSelect2Locales() { $('select.js-select-locale').select2(getLocalesConf()); }
export function loadSelect2TagCategories() { $('select.js-select-tag-category').select2(getTagCategoriesConf()); }

function getCountriesConf() {
    return {
        templateSelection: function (country) {
            if (!country.id) {
                return '';
            }

            var $country = $(
                '<span class="flag-' + country.element.value.toLowerCase() + '"></span><span>' + country.text + '</span>'
            );

            return $country;
        },
        templateResult: function (country) {
            if (!country.id) {
                return $('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
            }

            let $country = $(
                '<span class="flag-' + country.element.value.toLowerCase() + '"></span><span>' + country.text + '</span>'
            );

            return $country;
        },
        language: {
            noResults: function () {
                return Translator.trans('select2.no_results');
            },
            searching: function () {
                return Translator.trans('select2.searching');
            }
        },
        sorter: function (data) {
            if(data && data.length>1 && data[0].rank){
                data.sort(function(a,b) {return (a.rank > b.rank) ? -1 : ((b.rank > a.rank) ? 1 : 0);} );
            }

            return data;
        },
        matcher:function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            if (typeof data.text === 'undefined') {
                return null;
            }

            var idx = data.text.toLowerCase().indexOf(params.term.toLowerCase());
            if (idx > -1) {
                var modifiedData = $.extend({
                    'rank':(params.term.length / data.text.length)+ (data.text.length-params.term.length-idx)/(3*data.text.length)
                }, data, true);

                return modifiedData;
            }

            return null;
        }
    };
}

function getLocalesConf() {
    return {
        templateSelection: function (locale) {
            if (!locale.id) {
                return '';
            }

            var $locale = $(
                '<span class="flag-' + locale.element.value.toLowerCase() + '"></span><span class="flag-label">' + locale.text + '</span>'
            );

            return $locale;
        },
        templateResult: function (locale) {
            if (!locale.id) {
                return $('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
            }

            let $locale = $(
                '<span class="flag-' + locale.element.value.toLowerCase() + '"></span><span class="flag-label">' + locale.text + '</span>'
            );

            return $locale;
        },
        language: {
            noResults: function () {
                return Translator.trans('select2.no_results');
            },
            searching: function () {
                return Translator.trans('select2.searching');
            }
        }
    };
}

function getTagCategoriesConf() {
    return {
        templateSelection: function (category) {
            if (!category.id) {
                return '';
            }
            var $category = $(
                '<span class="tag-category-select-option tag-category-color" style="background-color: ' + category.element.dataset.color + '" title="' + category.text +  '"></span>' +
                '<span>' + category.text + '</span>'
            );

            return $category;
        },
        templateResult: function (category) {
            if (!category.id) {
                return $('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
            }

            var $category = $(
                '<span class="tag-category-select-option tag-category-color" style="background-color: ' + category.element.dataset.color + '" title="' + category.text +  '"></span>' +
                '<span>' + category.text + '</span>'
            );


            return $category;
        },
        language: {
            noResults: function () {
                return Translator.trans('select2.no_results');
            },
            searching: function () {
                return Translator.trans('select2.searching');
            }
        }
    };
}

function getDefaultConf() {
    return {
        templateSelection: function (element) {
            if (!element.id) {
                return '';
            }

            return element.text;
        },
        templateResult: function (element) {
            if (!element.id) {
                return $('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
            }

            return element.text;
        },
        language: {
            noResults: function () {
                return Translator.trans('select2.no_results');
            },
            searching: function () {
                return Translator.trans('select2.searching');
            }
        },
        sorter: function (data) {
            if(data && data.length>1 && data[0].rank){
                data.sort(function(a,b) {return (a.rank > b.rank) ? -1 : ((b.rank > a.rank) ? 1 : 0);} );
            }

            return data;
        },
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            if (typeof data.text === 'undefined') {
                return null;
            }

            var idx = data.text.toLowerCase().indexOf(params.term.toLowerCase());
            if (idx > -1) {
                var modifiedData = $.extend({
                    'rank':(params.term.length / data.text.length)+ (data.text.length-params.term.length-idx)/(3*data.text.length)
                }, data, true);

                return modifiedData;
            }

            return null;
        }
    }
}
