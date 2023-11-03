import DefaultController from "./default_controller"
import Translator from "bazinga-translator";
import { htmlStringToDomElement } from "../../js/utils";

/* stimulusFetch: 'lazy' */
export default class extends DefaultController {
    templateSelection(category) {
        if (!category.id) {
            return htmlStringToDomElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
        }

        return htmlStringToDomElement('<div><span class="tag-category-select-option tag-category-color" style="background-color: ' + category.element.dataset.color + '"></span><span>' + category.text +'</span></div>');
    }

    templateResult(category) {
        if (!category.id) {
            return htmlStringToDomElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
        }

        return htmlStringToDomElement('<div><span class="tag-category-select-option tag-category-color" style="background-color: ' + category.element.dataset.color + '"></span><span>' + category.text + '</span></div>');
    }
}
