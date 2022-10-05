import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'checkbox'];

    updateInput() {
        let ids = [];

        this.checkboxTargets.forEach((checkbox) => {
            ids.push(checkbox.dataset.id);
        });

        this.inputTarget.value = ids.join();
    }

    update(event) {
        let checked = event.target.checked;
        let checkboxes = event.target.closest('ul').getElementsByTagName('input');
        for (const checkbox of checkboxes) {
            checkbox.checked = checked;
        }

        this.updateInput();
    }
}
