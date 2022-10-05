import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";
import { TsSelect2 } from "../../node_modules/ts-select2/dist/core";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        let self = this;

        new TsSelect2(this.element, {
            templateSelection: function (element) {
                if (!element.id) {
                    return self.htmlToElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
                }

                return element.text;
            },
            templateResult: function (element) {
                if (!element.id && !element.children) {
                    return self.htmlToElement('<div><span class="select-placeholder">' + Translator.trans('select2.none') + '</span></div>');
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
            }
        })
    }

    htmlToElement(html) {
        let template = document.createElement('template');
        html = html.trim();
        template.innerHTML = html;
        return template.content.firstChild;
    }
}
