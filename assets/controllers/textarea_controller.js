import { Controller } from '@hotwired/stimulus';
import { M } from '@materializecss/materialize';

export default class extends Controller {
    connect() {
        M.textareaAutoResize(this.element);
    }
}
