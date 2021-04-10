import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['frame', 'element']

    display(event) {
        event.preventDefault();
        
        this.frameTarget.querySelector('a').href = event.originalTarget.dataset.image;
        this.frameTarget.querySelector('a').dataset.title = event.originalTarget.closest('a').dataset.title;
        this.frameTarget.querySelector('img').src = event.originalTarget.src;
        this.frameTarget.querySelector('.image-label').innerHTML = event.originalTarget.closest('a').dataset.title;

        this.elementTargets.forEach((element) => {
            element.classList.remove('active');
        })
        event.originalTarget.closest('a').classList.add('active');
    }
}
