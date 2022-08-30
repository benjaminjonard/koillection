import { Controller } from 'stimulus';
import Sortable from "sortablejs";

export default class extends Controller {
    static targets = ['checkedHolder', 'uncheckedHolder']

    connect() {
        let options = {
            draggable: '.column',
            handle: '.handle',
        }

        new Sortable(this.checkedHolderTarget, options);
    }

    check(event) {
        if (event.target.checked) {
            this.checkedHolderTarget.appendChild(event.target.closest('.column'))
            event.target.closest('.column').getElementsByClassName('handle')[0].classList.remove('visibility-none');
        } else {
            this.uncheckedHolderTarget.appendChild(event.target.closest('.column'))
            event.target.closest('.column').getElementsByClassName('handle')[0].classList.add('visibility-none');
        }
    }
}
