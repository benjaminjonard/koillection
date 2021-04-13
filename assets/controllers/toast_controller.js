import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        M.toast({
            html: this.element.dataset.message,
            displayLength: 5000
        })
    }
}
