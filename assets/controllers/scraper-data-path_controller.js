import { Controller } from '@hotwired/stimulus';
import Sortable from "sortablejs";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['dataPath', 'button']

    index = null;

    connect() {
        this.index = this.dataPathTargets.length;
        this.computePositions();

        let self = this;
        new Sortable(this.element, {
            draggable: '.dataPath',
            handle: '.handle',
            onSort: function () {
                self.computePositions();
            }
        });
    }

    add(event) {
        event.preventDefault();
        let prototype = this.element.dataset.prototype;
        let newForm = prototype.replace(/__name__/g, this.index);
        this.buttonTarget.insertAdjacentHTML('beforebegin', newForm);
        this.index++;
        this.computePositions();
    }

    remove(event) {
        event.preventDefault();
        event.target.closest('.dataPath').remove();
        this.computePositions();
    }

    computePositions() {
        this.element.querySelectorAll('.position').forEach((element, index) => {
            element.value = index+1;
        })
    }
}
