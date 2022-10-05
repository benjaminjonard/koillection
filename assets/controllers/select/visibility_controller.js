import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";
import { TsSelect2 } from "../../node_modules/ts-select2/dist/core";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        let self = this;

        new TsSelect2(this.element, {
            templateSelection: function (visibility) {
                if (!visibility.id) {
                    return self.htmlToElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
                }

                return self.htmlToElement(
                    '<div>' + self.getIcon(visibility.element.value) + '<span>' + visibility.text + '</span></div>'
                );
            },

            templateResult: function (visibility) {
                if (!visibility.id) {
                    return '';
                }

                return self.htmlToElement(
                    '<div>' + self.getIcon(visibility.element.value) + '<span>' + visibility.text + '</span><span class="select-tip">' + Translator.trans('global.visibilities.' + visibility.id + '.description') + '</span></div>'
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

    getIcon(value) {
        switch (value) {
            case 'public':
                return '<i class="select-icon fa fa-globe fa-fw"></i><i class="select-icon fa fa-unlock-alt fa-fw fa-secondary"></i>';
            case 'private':
                return '<i class="select-icon fa fa-lock fa-fw"></i>';
            case 'internal':
                return '<i class="select-icon fa fa-user fa-fw"></i><i class="select-icon fa fa-unlock-alt fa-fw fa-secondary"></i>';
        }

        return '';
    }
}
