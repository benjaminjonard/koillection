import { Controller } from 'stimulus';
import Translator from "../../js/translator.min";
import { TsSelect2 } from "../../node_modules/ts-select2/dist/core";

export default class extends Controller {
    connect() {
        let self = this;

        new TsSelect2(this.element, {
            templateSelection: function (category) {
                if (!category.id) {
                    return '';
                }

                return self.htmlToElement('<div><span class="tag-category-select-option tag-category-color" style="background-color: ' + category.element.dataset.color + '"></span><span>' + category.text +'</span></div>');
            },
            templateResult: function (category) {
                if (!category.id) {
                    return self.htmlToElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
                }

                return self.htmlToElement('<div><span class="tag-category-select-option tag-category-color" style="background-color: ' + category.element.dataset.color + '"></span><span>' + category.text + '</span></div>');
            },
            language: {
                noResults: function () {
                    return Translator.trans('select2.no_results');
                },
                searching: function () {
                    return Translator.trans('select2.searching');
                }
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
