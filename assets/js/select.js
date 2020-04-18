import Translator from "./translator.min";

export function loadSelect2()
{
    $('select:not(.js-select-country)').select2(getDefaultConf());
}

export function loadSelect2Countries()
{
    $('select.js-select-country').select2(getCountriesConf());
}

export function loadSelect2Locales()
{
    $('select.js-select-locale').select2(getLocalesConf());
}

export function loadSelect2Themes()
{
    $('select.js-select-theme').select2(getThemesConf());
}

function getCountriesConf() {
    return {
        templateSelection: function (country) {
            if (!country.id) {
                return country.text;
            }

            var $country = $(
                '<span class="flag-' + country.element.value.toLowerCase() + '"></span><span>' + country.text + '</span>'
            );

            return $country;
        },
        templateResult: function (country) {
            if (!country.id) {
                return country.text;
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
                return locale.text;
            }

            var $locale = $(
                '<span class="flag-' + locale.element.value.toLowerCase() + '"></span><span class="flag-label">' + locale.text + '</span>'
            );

            return $locale;
        },
        templateResult: function (locale) {
            if (!locale.id) {
                return locale.text;
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

function getThemesConf() {
    return {
        templateSelection: function (theme) {
            if (!theme.id) {
                return theme.text;
            }

            var $theme = $(
                '<span class="theme-preview ' + theme.element.value + ' dark"></span>' +
                '<span class="theme-preview ' + theme.element.value + ' medium"></span>' +
                '<span class="theme-preview ' + theme.element.value + ' light"></span>' +
                '<span class="theme-preview ' + theme.element.value + ' lighter"></span>' +
                '<span class="theme-preview ' + theme.element.value + ' complementary"></span>' +
                '<span class="theme-label">' + theme.text + '</span>'
            );

            return $theme;
        },
        templateResult: function (theme) {
            if (!theme.id) {
                return theme.text;
            }

            var $theme = $(
                '<span class="theme-preview ' + theme.element.value + ' dark"></span>' +
                '<span class="theme-preview ' + theme.element.value + ' medium"></span>' +
                '<span class="theme-preview ' + theme.element.value + ' light"></span>' +
                '<span class="theme-preview ' + theme.element.value + ' lighter"></span>' +
                '<span class="theme-preview ' + theme.element.value + ' complementary"></span>' +
                '<span class="theme-label">' + theme.text + '</span>'
            );

            return $theme;

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

function getDefaultConf()
{
    return {
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
