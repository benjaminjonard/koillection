import { Controller } from 'stimulus';
import { Dropdown } from 'materialize-css';

export default class extends Controller {
    connect() {
        M.Dropdown.init(this.element);
    }
}
