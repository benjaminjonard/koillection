import { Controller } from 'stimulus';
import Sortable from "sortablejs";

export default class extends Controller {
    static targets = ['position']

    connect() {
        let self = this;
        new Sortable(this.element, {
            draggable: '.datum',
            handle: '.handle',
            onSort: function () {
                self.computePositions();
            }
        });
    }

    computePositions() {
        this.positionTargets.forEach((element, index) => {
            element.value = index+1;
        })
    }
}
