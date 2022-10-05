import { Controller } from '@hotwired/stimulus';
import { toast } from '@materializecss/materialize';

export default class extends Controller {
    connect() {
        M.toast({
            html: this.element.dataset.message,
            displayLength: 5000
        })
    }
}
