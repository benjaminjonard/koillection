import { Controller } from 'stimulus';
import { Dropdown } from '@materializecss/materialize';

export default class extends Controller {
    static targets = ['list'];

    displayChoices(event) {
        if (event.target.value == 'list') {
            this.listTarget.classList.remove('hidden');
        } else {
            this.listTarget.classList.add('hidden');
        }
    }
}
