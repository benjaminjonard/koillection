import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        M.Dropdown.init(this.element);
    }
}
