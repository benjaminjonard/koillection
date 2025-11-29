import { Controller } from '@hotwired/stimulus';
import Sortable from "sortablejs";
import Translator from "bazinga-translator";

export default class extends Controller {
    static targets = ['help']

    updateHelp(event) {
        let value = event.target.value;
        let help = '';

        if (value === 'date' || value === 'checkbox' || value === 'price' || value === 'country') {
            help = Translator.trans('label.expected_format') + ' ' + Translator.trans('label.' + value + '.format')
        }

        this.helpTarget.innerHTML = help;
    }
}
