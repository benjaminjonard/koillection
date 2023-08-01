import { Controller } from '@hotwired/stimulus';
import { M } from '@materializecss/materialize';

export default class extends Controller {
    connect() {
        M.toast({
            text: this.element.dataset.message,
            classes: this.element.dataset.classes,
            displayLength: 5000
        })
    }
}
