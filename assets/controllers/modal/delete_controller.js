import ModalController from './default_controller.js';

export default class extends ModalController {
    onOpen(trigger) {
        this.element.getElementsByTagName('form')[0].action = trigger.dataset.path;
        this.element.getElementsByClassName('modal-body')[0].innerHTML = trigger.dataset.message;
    }
}
