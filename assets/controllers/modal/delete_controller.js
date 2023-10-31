import { Controller } from '@hotwired/stimulus';

import '../../styles/modal.css'

export default class extends Controller {
    connect() {
        M.Modal.init(this.element, {
            onOpenStart: function (modal, trigger) {
                modal.getElementsByTagName('form')[0].action = trigger.dataset.path;
                modal.getElementsByClassName('modal-body')[0].innerHTML = trigger.dataset.message;
            }
        });
    }
}
