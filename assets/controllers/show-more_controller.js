import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['showMoreButton', 'showLessButton', 'hideable'];

    showMore(event) {
        event.preventDefault();
        this.showMoreButtonTarget.classList.add('hidden');
        this.showLessButtonTarget.classList.remove('hidden');
        this.hideableTargets.forEach((element) => {
            element.classList.remove('hidden')
        });
    }

    showLess(event) {
        event.preventDefault();
        this.showLessButtonTarget.classList.add('hidden');
        this.showMoreButtonTarget.classList.remove('hidden');
        this.hideableTargets.forEach((element) => {
            element.classList.add('hidden')
        });
    }
}
