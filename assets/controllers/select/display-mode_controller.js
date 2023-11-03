import DefaultController from "./default_controller"
import Translator from "bazinga-translator";
import { htmlStringToDomElement } from "../../js/utils";

/* stimulusFetch: 'lazy' */
export default class extends DefaultController {
    templateSelection(displayMode) {
        if (!displayMode.id) {
            return htmlStringToDomElement('<span class="select-placeholder">' + Translator.trans('select2.none') + '</span>');
        }

        let icon = displayMode.element.value == 'grid' ? 'th' : 'list';

        return htmlStringToDomElement(
            '<div><i class="select-icon fa fa-' + icon + ' fa-fw"></i><span>' + displayMode.text + '</span></div>'
        );
    }

    templateResult(displayMode) {
        if (!displayMode.id) {
            return htmlStringToDomElement('<div><span class="select-placeholder">' + Translator.trans('select2.none') + '</span></div>');
        }

        let icon = displayMode.element.value == 'grid' ? 'th' : 'list';

        return htmlStringToDomElement(
            '<div><i class="select-icon fa fa-' + icon + ' fa-fw"></i><span>' + displayMode.text + '</span></div>'
        );
    }
}
