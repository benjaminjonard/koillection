import { Controller } from '@hotwired/stimulus';
import Sortable from "sortablejs";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['value', 'button', 'input', 'label']

    index = null;

    connect() {
        this.index = this.valueTargets.length;

        let self = this;
        new Sortable(this.element, {
            draggable: '.value',
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
    }

    remove(event) {
        event.preventDefault();
        event.target.closest('.value').remove();
    }

    computePositions() {
        let self = this;
        this.valueTargets.forEach((element, index) => {
            self.inputTargets[index].id = 'choice_list_choices_' + index;
            self.inputTargets[index].setAttribute('name', 'choice_list[choices][' + index + ']');
            self.labelTargets[index].setAttribute('for', 'choice_list[choices][' + index + ']');
        })
    }
}
