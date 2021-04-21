import { Controller } from 'stimulus';
import Sortable from "sortablejs";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['field', 'button', 'position']

    index = null;

    connect() {
        this.index = this.fieldTargets.length;
        this.computePositions();

        let self = this;
        new Sortable(this.element, {
            draggable: '.field',
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
        event.target.closest('.field').remove();
        this.computePositions();
    }

    computePositions() {
        this.positionTargets.forEach((element, index) => {
            element.value = index+1;
        })
    }
}
