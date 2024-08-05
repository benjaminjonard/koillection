import { Controller } from '@hotwired/stimulus';
import { useHotkeys } from "stimulus-use/hotkeys";

export default class extends Controller {
    static targets = ['next', 'previous'];

    lightboxOpened = false;

    connect() {
        let self = this;

        useHotkeys(this, {
            left: [this.previous],
            right: [this.next],
        })

        document.addEventListener('lightGalleryOpened', function (event) {
            self.lightboxOpened = true;
        });

        document.addEventListener('lightGalleryClosed', function (event) {
            self.lightboxOpened = false;
        });
    }

    next(event) {
        event.preventDefault();
        if (this.hasNextTarget && !this.lightboxOpened) {
            window.location = this.nextTarget.href;
        }
    }

    previous(event) {
        event.preventDefault();
        if (this.hasPreviousTarget && !this.lightboxOpened) {
            window.location = this.previousTarget.href;
        }
    }
}
