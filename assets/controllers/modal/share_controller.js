import { Controller } from '@hotwired/stimulus';
import { M } from '@materializecss/materialize';
import '../../styles/modal.css'

export default class extends Controller {
    static targets = ['message']

    connect() {
        let self = this;
        M.Modal.init(this.element, {
            onOpenStart: function (modal, trigger) {
                if (modal.dataset.userVisibility === 'private') {
                    self.messageTarget.innerHTML = modal.dataset.messagePrivateUser;
                } else if (trigger.dataset.visibility === 'private') {
                    self.messageTarget.innerHTML = modal.dataset.messagePrivateEntity;
                } else {
                    let message =  modal.dataset.messageShareLink + '<br><b><a href="__placeholder_path__">__placeholder_path__</a></b>';
                    self.messageTarget.innerHTML = message.replace(/__placeholder_path__/g, trigger.dataset.path);
                }
            }
        });
    }
}
