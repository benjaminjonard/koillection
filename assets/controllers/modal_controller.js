import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        M.Modal.init(this.element);
    }
}
