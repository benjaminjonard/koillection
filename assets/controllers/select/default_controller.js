import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";
import { TsSelect2 } from "../../node_modules/ts-select2/dist/core";
import { htmlStringToDomElement } from "../../js/utils";

export default class extends Controller {
    select2;

    connect() {
        this.loadSelect();
    }

    loadSelect() {
        this.select2 = new TsSelect2(this.element, {
            minimumResultsForSearch: this.minimumResultsForSearch,
            language: {
                noResults: function () {
                    return Translator.trans('select2.no_results');
                },
                searching: function () {
                    return Translator.trans('select2.searching');
                }
            },
            sorter: function (data) {
                if (data && data.length>1 && data[0].rank) {
                    data.sort(function(a,b) {return (a.rank > b.rank) ? -1 : ((b.rank > a.rank) ? 1 : 0);} );
                }

                return data;
            },
            matcher: function(params, data) {
                if (typeof params.term === 'undefined' || params.term.trim() === '') {
                    return data;
                }

                if (typeof data.text === 'undefined') {
                    return null;
                }

                let idx = data.text.toLowerCase().indexOf(params.term.toLowerCase());
                if (idx > -1) {
                    let rank = {
                        'rank': (params.term.length / data.text.length) + (data.text.length-params.term.length-idx)/(3*data.text.length)
                    };

                    return {...rank, ...data};
                }

                return null;
            },

            templateSelection: this.templateSelection,
            templateResult: this.templateResult
        })
    }

    templateSelection(element) {
        if (!element.text) {
            return htmlStringToDomElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
        }

        return htmlStringToDomElement('<div><span>' + element.text + '</span></div>');
    }

    templateResult(element) {
        if (!element.text && !element.children) {
            return htmlStringToDomElement('<div><span class="select-placeholder">' + Translator.trans('select2.none') + '</span></div>');
        }

        return htmlStringToDomElement('<div><span>' + element.text + '</span></div>');
    }

    minimumResultsForSearch() {
        return 1;
    }

    update({ detail: { value } }) {
        this.select2.val(value);
    }
}
