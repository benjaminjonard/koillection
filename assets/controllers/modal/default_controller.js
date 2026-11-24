import { Controller } from '@hotwired/stimulus';

import '../../styles/modal.css'

export default class extends Controller {
    connect() {
        this.handleDocumentClick = this.handleDocumentClick.bind(this);
        this.handleModalClick = this.handleModalClick.bind(this);
        document.addEventListener('click', this.handleDocumentClick);
        this.element.addEventListener('click', this.handleModalClick);
    }

    disconnect() {
        document.removeEventListener('click', this.handleDocumentClick);
        this.element.removeEventListener('click', this.handleModalClick);
    }

    handleDocumentClick(event) {
        const trigger = event.target.closest('.modal-trigger');
        if (trigger === null) {
            return;
        }

        const targetId = trigger.dataset.target ?? (trigger.getAttribute('href') ?? '').replace('#', '');
        if (targetId !== this.element.id) {
            return;
        }

        event.preventDefault();
        this.onOpen(trigger);
        this.element.showModal();
        this.element.focus();
    }

    handleModalClick(event) {
        if (event.target === this.element || event.target.closest('.modal-close')) {
            this.element.close();
        }
    }

    onOpen(trigger) {
    }
}
