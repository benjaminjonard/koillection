import { Controller } from 'stimulus';

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

        event.target.closest('ul').getElementsByTagName('input').forEach((checkbox) => {
            checkbox.checked = checked;
        });

        this.updateInput();
    }
}
