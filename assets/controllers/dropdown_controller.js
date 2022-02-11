import { Controller } from 'stimulus';
import { Dropdown } from '@materializecss/materialize';

export default class extends Controller {
    connect() {
        M.Dropdown.init(this.element);
    }
}
