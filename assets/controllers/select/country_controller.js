import { Controller } from 'stimulus';
import Translator from "../../js/translator.min";
import { TsSelect2 } from "../../node_modules/ts-select2/dist/core";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        let self = this;

        new TsSelect2(this.element, {
            templateSelection: function (country) {
                if (!country.id) {
                    return '';
                }
                console.log(country)
                return self.htmlToElement(
                    '<div><span class="select-icon">' + country.element.dataset.flag + '</span><span>' + country.text + '</span></div>'
                );
            },
            templateResult: function (country) {
                if (!country.id) {
                    return self.htmlToElement('<div><span class="select-placeholder">' + Translator.trans('select2.none') + '</span></div>');
                }

                return self.htmlToElement(
                    '<div><span class="select-icon">' + country.element.dataset.flag + '</span><span>' + country.text + '</span></div>'
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
