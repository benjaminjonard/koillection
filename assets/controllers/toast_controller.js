import { Controller } from 'stimulus';
import { toast } from 'materialize-css';

export default class extends Controller {
    connect() {
        M.toast({
            html: this.element.dataset.message,
            displayLength: 5000
        })
    }
}
