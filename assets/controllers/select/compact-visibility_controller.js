import DefaultController from "./default_controller"
import Translator from "bazinga-translator";
import { htmlStringToDomElement, getVisibilityIcon } from "../../js/utils";

/* stimulusFetch: 'lazy' */
export default class extends DefaultController {
    templateSelection(visibility) {
        if (!visibility.id) {
            return htmlStringToDomElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
        }

        return htmlStringToDomElement(
            '<div>' + getVisibilityIcon(visibility.element.value) + '</div>'
        );
    }

    templateResult(visibility) {
        if (!visibility.id) {
            return '';
        }

        return htmlStringToDomElement(
            '<div>' + getVisibilityIcon(visibility.element.value) + '</div>'
        );
    }
}
