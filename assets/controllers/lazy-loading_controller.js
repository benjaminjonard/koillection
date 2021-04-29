import {Controller} from 'stimulus';
import { useIntersection } from 'stimulus-use'

export default class extends Controller {
    static targets = ['image'];

    connect() {
        useIntersection(this, this.options)
    }

    appear() {
        this.imageTarget.src = this.imageTarget.dataset.src;
        this.imageTarget.removeAttribute('data-src');
        this.element.classList.remove('placeholder');
    }
}
