import { Controller } from 'stimulus';
import Translator from "../js/translator.min";
import '../styles/select2.css';

export default class extends Controller {
    connect() {
        $(this.element).select2({
            templateSelection: function (category) {
                if (!category.id) {
                    return '';
                }
                return $(
                    '<span class="tag-category-select-option tag-category-color" style="background-color: ' + category.element.dataset.color + '" title="' + category.text +  '"></span>' +
                    '<span>' + category.text + '</span>'
                );
            },
            templateResult: function (category) {
                if (!category.id) {
                    return $('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
                }

                return $(
                    '<span class="tag-category-select-option tag-category-color" style="background-color: ' + category.element.dataset.color + '" title="' + category.text +  '"></span>' +
                    '<span>' + category.text + '</span>'
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
