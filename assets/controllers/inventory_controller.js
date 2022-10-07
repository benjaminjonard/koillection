import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['pills'];

    update(event) {
        let add = event.target.checked ? 1 : -1;
        let self = this;
        let target = event.target;

        let formData = new FormData();
        formData.append('id', event.target.dataset.id);
        formData.append('checked', event.target.checked);

        fetch('/inventories/' + this.element.dataset.inventoryId + '/check', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(function (results) {
            self.pillsTarget.innerHTML = results.htmlForNavPills;

            self.getParentCollections(target).forEach((collection, index) => {
                //Update value
                let counter = collection.querySelector('.js-checked-counter');
                let newValue = parseInt(counter.innerHTML) + add;
                counter.innerHTML = newValue;

                //Update rate
                let rate = collection.querySelector('.js-rate');
                let totalCounter = parseInt(collection.querySelector('.js-total-counter').innerHTML);
                let newRate = (newValue * 100) / totalCounter;
                rate.innerHTML = Math.round(newRate * 100) / 100;

                //Update colors
                let title = collection.querySelector('.card-panel');
                if (totalCounter === newValue) {
                    title.classList.remove('red', 'lighten-4');
                    title.classList.add('green', 'lighten-4');
                } else {
                    title.classList.remove('green', 'lighten-4');
                    title.classList.add('red', 'lighten-4');
                }
            });
        })
    }

    getParentCollections(element) {
        let array = [];
        for (let n in element) {
            element = element.parentNode;
            if (element.id === 'inventory-root') {
                break;
            }

            if (element.classList.contains('inventory-collection-show')) {
                array.push(element);
            }
        }

        return array;
    }
}
