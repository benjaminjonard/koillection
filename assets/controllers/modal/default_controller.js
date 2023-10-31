import { Controller } from '@hotwired/stimulus';

import '../../styles/modal.css'

export default class extends Controller {
    connect() {
        M.Modal.init(this.element, {
            preventScrolling: false
        });
    }
}
