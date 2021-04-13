import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'label'];

    fillInputWithSuggestion(event) {
        this.inputTarget.value = event.target.dataset.suggestion;
        this.labelTarget.classList.add('active');
    }
}
