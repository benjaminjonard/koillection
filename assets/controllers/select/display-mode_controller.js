import { Controller } from 'stimulus';
import Translator from "../../js/translator.min";
import { TsSelect2 } from "../../node_modules/ts-select2/dist/core";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        let self = this;

        new TsSelect2(this.element, {
            templateSelection: function (displayMode) {
                if (!displayMode.id) {
                    return self.htmlToElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
                }

                let icon = displayMode.element.value == 'grid' ? 'th' : 'list';

                return self.htmlToElement(
                    '<div><i class="select-icon fa fa-' + icon + ' fa-fw"></i><span>' + displayMode.text + '</span></div>'
                );
            },
            templateResult: function (displayMode) {
                if (!displayMode.id) {
                    return self.htmlToElement('<div><span class="select-placeholder">' + Translator.trans('select2.none') + '</span></div>');
                }

                let icon = displayMode.element.value == 'grid' ? 'th' : 'list';

                return self.htmlToElement(
                    '<div><i class="select-icon fa fa-' + icon + ' fa-fw"></i><span>' + displayMode.text + '</span></div>'
                );
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
