import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['row', 'sorter']

    sortDesc(event) {
        const column = event.target.dataset.column;
        const values = this.element.querySelectorAll(`tbody td[data-column="${column}"]`);
        const orderedValues = Array.from(values).sort((a, b) => (a.dataset.value > b.dataset.value) ? 1 : -1)

        orderedValues.forEach((value, index) => {
            this.element.querySelector('tbody').appendChild(value.closest('tr'));
        });

        this.sorterTargets.forEach(sorter => {
           sorter.classList.remove('active');
        });
        event.target.classList.add('active')
    }

    sortAsc(event) {
        const column = event.target.dataset.column;
        const values = this.element.querySelectorAll(`tbody td[data-column="${column}"]`);
        const orderedValues = Array.from(values).sort((a, b) => (a.dataset.value <= b.dataset.value) ? 1 : -1)

        this.element.querySelector('tbody').innerHTML = '';
        orderedValues.forEach((value, index) => {
            this.element.querySelector('tbody').appendChild(value.closest('tr'));
        });

        this.sorterTargets.forEach(sorter => {
            sorter.classList.remove('active');
        });
        event.target.classList.add('active')
    }
}
