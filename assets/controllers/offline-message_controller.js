import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    hide(event) {
        this.element.classList.add("hidden");
    }

    show(event) {
        this.element.classList.remove("hidden");
    }
}
