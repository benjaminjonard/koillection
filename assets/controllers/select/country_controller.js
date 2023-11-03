import DefaultController from "./default_controller"
import Translator from "bazinga-translator";
import { htmlStringToDomElement } from "../../js/utils";

/* stimulusFetch: 'lazy' */
export default class extends DefaultController {
    templateSelection(country) {
        if (!country.id) {
            return htmlStringToDomElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
        }

        return htmlStringToDomElement(
            '<div><span class="select-icon">' + country.element.dataset.flag + '</span><span>' + country.text + '</span></div>'
        );
    }

    templateResult(country) {
        if (!country.id) {
            return htmlStringToDomElement('<div><span class="select-placeholder">' + Translator.trans('select2.none') + '</span></div>');
        }

        return htmlStringToDomElement(
            '<div><span class="select-icon">' + country.element.dataset.flag + '</span><span>' + country.text + '</span></div>'
        );
    }
}
