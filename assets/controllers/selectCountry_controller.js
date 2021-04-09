import { Controller } from 'stimulus';
import Translator from "../js/translator.min";
import '../styles/select2.css';

export default class extends Controller {
    connect() {
        $(this.element).select2({
            templateSelection: function (country) {
                if (!country.id) {
                    return '';
                }

                return $(
                    '<span class="flag-' + country.element.value.toLowerCase() + '"></span><span>' + country.text + '</span>'
                );
            },
            templateResult: function (country) {
                if (!country.id) {
                    return $('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
                }

                return $(
                    '<span class="flag-' + country.element.value.toLowerCase() + '"></span><span>' + country.text + '</span>'
                );
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

                let idx = data.text.toLowerCase().indexOf(params.term.toLowerCase());
                if (idx > -1) {
                    return $.extend({
                        'rank':(params.term.length / data.text.length)+ (data.text.length-params.term.length-idx)/(3*data.text.length)
                    }, data, true);
                }

                return null;
            }
        })
    }
}
