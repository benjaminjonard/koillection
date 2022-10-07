import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['image', 'thumbnail']

    display(event) {
        event.preventDefault();

        this.imageTargets.forEach((element) => {
            if (element.dataset.id === event.target.dataset.id) {
                element.classList.add('active');
            } else {
                element.classList.remove('active');
            }
        })

        this.thumbnailTargets.forEach((element) => {
            element.classList.remove('active');
        })
        event.target.classList.add('active');
    }
}
