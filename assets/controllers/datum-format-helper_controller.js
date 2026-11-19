import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";

export default class extends Controller {
    static targets = ['help']

    updateHelp(event) {
        let value = event.target.value;
        let help = '';

        const formats = {
            date: 'YYYY-MM-DD',
            price: '1 USD, 4.5 EUR...',
            country: 'US, FR...',
            checkbox: Translator.trans('label.checkbox.format'),
        };

        if (value in formats) {
            help = Translator.trans('label.expected_format') + ' ' + formats[value];
        }

        this.helpTarget.innerHTML = help;
    }
}
