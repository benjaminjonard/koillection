import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['filters', 'showFiltersBtn', 'hideFiltersBtn']

    showFilters(event) {
        this.filtersTarget.classList.remove('hidden');

        this.showFiltersBtnTarget.classList.add('hidden');
        this.hideFiltersBtnTarget.classList.remove('hidden');
    }

    hideFilters(event) {
        this.filtersTarget.classList.add('hidden');

        this.showFiltersBtnTarget.classList.remove('hidden');
        this.hideFiltersBtnTarget.classList.add('hidden');
    }
}
