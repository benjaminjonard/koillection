import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['frame', 'element']

    display(event) {
        event.preventDefault();

        this.frameTarget.querySelector('a').href = event.target.dataset.image;
        this.frameTarget.querySelector('a').dataset.title = event.target.closest('a').dataset.title;
        this.frameTarget.querySelector('img').src = event.target.src;
        this.frameTarget.querySelector('.image-label').innerHTML = event.target.closest('a').dataset.title;

        this.elementTargets.forEach((element) => {
            element.classList.remove('active');
        })
        event.target.closest('a').classList.add('active');
    }
}
