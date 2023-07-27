import { Controller } from '@hotwired/stimulus';
import Sortable from "sortablejs";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['dataPath', 'button', 'position']

    index = null;

    connect() {
        this.index = this.dataPathTargets.length;

        let self = this;
        new Sortable(this.element, {
            draggable: '.dataPath',
            handle: '.handle'
        });
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
        event.target.closest('.dataPath').remove();
    }
}
