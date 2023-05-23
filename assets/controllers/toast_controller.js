import { Controller } from '@hotwired/stimulus';
import { M } from '@materializecss/materialize';

export default class extends Controller {
    connect() {
        M.toast({
            unsafeHtml: this.element.dataset.message,
            displayLength: 500000000
        })
    }
}
