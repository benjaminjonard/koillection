import { Controller } from 'stimulus';
import Sortable from "sortablejs";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['value', 'button']

    index = null;

    connect() {
        this.index = this.valueTargets.length;
    }

    add(event) {
        event.preventDefault();
        let prototype = this.element.dataset.prototype;
        let newForm = prototype.replace(/__name__/g, this.index);
        this.buttonTarget.insertAdjacentHTML('beforebegin', newForm);
        this.index++;
    }

    remove(event) {
        event.preventDefault();
        event.target.closest('.value').remove();
    }
}
