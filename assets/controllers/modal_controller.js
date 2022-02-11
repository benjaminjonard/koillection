import { Controller } from 'stimulus';
import { Modal } from '@materializecss/materialize';
import '../styles/modal.css'

export default class extends Controller {
    connect() {
        M.Modal.init(this.element);
    }
}
