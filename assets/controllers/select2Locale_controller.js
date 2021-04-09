import { Controller } from 'stimulus';
import Translator from "../js/translator.min";

export default class extends Controller {
    connect() {
        $(this.element).select2({
            templateSelection: function (locale) {
                if (!locale.id) {
                    return '';
                }

                return $(
                    '<span class="flag-' + locale.element.value.toLowerCase() + '"></span><span class="flag-label">' + locale.text + '</span>'
                );
            },
            templateResult: function (locale) {
                if (!locale.id) {
                    return $('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
                }

                return $(
                    '<span class="flag-' + locale.element.value.toLowerCase() + '"></span><span class="flag-label">' + locale.text + '</span>'
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
}
