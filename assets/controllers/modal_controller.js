import { Controller } from 'stimulus';
import { Modal } from 'materialize-css';
import '../styles/modal.css'

export default class extends Controller {
    connect() {
        M.Modal.init(this.element);
    }
}
