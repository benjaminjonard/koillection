import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        M.Forms.InitTextarea(this.element);
        M.Forms.textareaAutoResize(this.element);
    }
}
