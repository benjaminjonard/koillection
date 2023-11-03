import DefaultController from "./default_controller"
import Translator from "bazinga-translator";
import { htmlStringToDomElement, getFlagFromCountryCode } from "../../js/utils";

/* stimulusFetch: 'lazy' */
export default class extends DefaultController {
    templateSelection(locale) {
        if (!locale.id) {
            return htmlStringToDomElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
        }

        return htmlStringToDomElement(
            '<div><span class="select-icon">' + getFlagFromCountryCode(locale.element.value) + '</span><span>' + locale.text + '</span></div>'
        );
    }

    templateResult(locale) {
        if (!locale.id) {
            return htmlStringToDomElement('<div><span class="select-placeholder">' + Translator.trans('select2.none') + '</span></div>');
        }

        return htmlStringToDomElement(
            '<div><span class="select-icon">' + getFlagFromCountryCode(locale.element.value) + '</span><span>' + locale.text + '</span></div>'
        );
    }
}
