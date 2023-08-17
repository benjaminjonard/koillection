import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";
import { TsSelect2 } from "../../node_modules/ts-select2/dist/core";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        let self = this;

        new TsSelect2(this.element, {
            templateSelection: function (locale) {
                if (!locale.id) {
                    return self.htmlToElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
                }

                return self.htmlToElement(
                    '<div><span class="select-icon">' + self.getFlagEmoji(locale.element.value) + '</span><span>' + locale.text + '</span></div>'
                );
            },
            templateResult: function (locale) {
                if (!locale.id) {
                    return self.htmlToElement('<div><span class="select-placeholder">' + Translator.trans('select2.none') + '</span></div>');
                }

                return self.htmlToElement(
                    '<div><span class="select-icon">' + self.getFlagEmoji(locale.element.value) + '</span><span>' + locale.text + '</span></div>'
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

    getFlagEmoji(countryCode) {
        if (countryCode === 'en') {
            countryCode = 'us';
        }

        if (countryCode.length === 5) {
            countryCode = countryCode.slice(-2);
        }

        console.log(countryCode)

        const codePoints = countryCode
            .toUpperCase()
            .split('')
            .map(char =>  127397 + char.charCodeAt(0));

        return String.fromCodePoint(...codePoints);
    }
}
