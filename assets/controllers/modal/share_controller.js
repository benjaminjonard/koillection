import ModalController from './default_controller.js';

export default class extends ModalController {
    static targets = ['message']

    onOpen(trigger) {
        const modal = this.element;
        if (modal.dataset.userVisibility === 'private') {
            this.messageTarget.innerHTML = modal.dataset.messagePrivateUser;
        } else if (trigger.dataset.visibility === 'private') {
            this.messageTarget.innerHTML = modal.dataset.messagePrivateEntity;
        } else {
            let message = modal.dataset.messageShareLink + '<br><b><a href="__placeholder_path__">__placeholder_path__</a></b>';
            this.messageTarget.innerHTML = message.replace(/__placeholder_path__/g, trigger.dataset.path);
        }
    }
}
