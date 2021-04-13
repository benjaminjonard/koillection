import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['menu'];

    boundHide = null;

    initialize() {
        this.boundHide = this.hide.bind(this);
    }

    show(event) {
        event.preventDefault();
        this.menuTarget.classList.remove('hidden');
        document.addEventListener('mouseup', this.boundHide);
    }

    hide(event) {
        if (this.menuTarget !== event.target.offsetParent) {
            this.menuTarget.classList.add('hidden');
            document.removeEventListener('mouseup', this.boundHide);
        }
    }
}
