import { Controller } from '@hotwired/stimulus';
import { useHotkeys } from "stimulus-use/hotkeys";

export default class extends Controller {
    static targets = ['next', 'previous'];

    connect() {
        useHotkeys(this, {
            left: [this.previous],
            right: [this.next],
        })
    }

    next(event) {
        event.preventDefault();
        if (this.hasNextTarget) {
            window.location = this.nextTarget.href;
        }
    }

    previous(event) {
        event.preventDefault();
        if (this.hasPreviousTarget) {
            window.location = this.previousTarget.href;
        }
    }
}
