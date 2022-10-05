import { Controller } from '@hotwired/stimulus';
import { useIntersection } from 'stimulus-use'

export default class extends Controller {
    static targets = ['image'];

    connect() {
        useIntersection(this, this.options)
    }

    appear() {
        let img = new Image()
        let src = this.imageTarget.dataset.src;
        let self = this;

        img.onload = function() {
            self.element.classList.remove("placeholder");
            if (!! self.element.parent) {
                self.element.replaceChild(img, el)
            } else {
                self.imageTarget.src = src;
            }
            self.imageTarget.removeAttribute('data-src');
            self.element.removeAttribute('data-controller');
        }

        if (src != null) {
            img.src = src;
        }
    }
}
