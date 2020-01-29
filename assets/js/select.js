import Translator from "./translator.min";

export function loadSelect2()
{
    $('select:not(.js-select-country)').select2(getDefaultConf());
}

export function loadSelect2Countries()
{
    $('select.js-select-country').select2(getCountriesConf());
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

function formatCountry(country) {

}