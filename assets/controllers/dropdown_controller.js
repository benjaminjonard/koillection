import { Controller } from '@hotwired/stimulus';
import { useClickOutside } from 'stimulus-use'

export default class extends Controller {
    static targets = ['menu'];

    connect() {
        useClickOutside(this)
    }

    show(event) {
        event.preventDefault();
        this.menuTarget.classList.remove('hidden');
    }

    clickOutside() {
        this.menuTarget.classList.add('hidden');
    }
}
