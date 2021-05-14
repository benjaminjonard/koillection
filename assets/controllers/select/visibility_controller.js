import { Controller } from 'stimulus';
import Translator from "../../js/translator.min";
import { TsSelect2 } from "../../node_modules/ts-select2/dist/core";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        let self = this;

        new TsSelect2(this.element, {
            templateSelection: function (visibility) {
                return self.htmlToElement(
                    '<div><span>' + visibility.text + '</span></div>'
                );
            },
            templateResult: function (visibility) {
                return self.htmlToElement(
                    '<div><span>' + visibility.text + '</span><span class="select-tip">' + Translator.trans('global.visibilities.' + visibility.id + '.description') + '</span></div>'
                );
            },
        })
    }

    htmlToElement(html) {
        let template = document.createElement('template');
        html = html.trim();
        template.innerHTML = html;
        return template.content.firstChild;
    }
}
