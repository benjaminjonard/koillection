import { Controller } from '@hotwired/stimulus';
import { M } from '@materializecss/materialize';

export default class extends Controller {
    static targets = ['input', 'formInput', 'result']

    timeout = null;
    autocompleteElement = null;
    items = [];

    connect() {
        let self = this;

        this.autocompleteElement = M.Autocomplete.init(this.inputTarget, {
            onAutocomplete: function (item) {
                self.onAutocomplete(item);
            }
        });

        if (this.formInputTarget.value.length > 0) {
            let values = JSON.parse(this.formInputTarget.value);
            let currentItems = [];

            for (const item of values) {
                this.resultTarget.insertAdjacentHTML('beforeend', this.getChip(item[0]));
                currentItems.push(item.id);
            }

            this.formInputTarget.value = JSON.stringify(currentItems);
        }
    }

    remove(event) {
        let existingItems = JSON.parse(this.formInputTarget.value);
        let relatedItem = event.target.closest('.related-item');
        let index = existingItems.indexOf(relatedItem.dataset.id);

        if (index > -1) {
            existingItems.splice(index, 1);
            relatedItem.remove();
        }

        this.formInputTarget.value = JSON.stringify(existingItems);
    }

    preventDefault(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    }

    autocomplete(event) {
        this.autocompleteElement.setMenuItems({});
        clearTimeout(this.timeout);
        let value = encodeURIComponent(this.inputTarget.value);
        let self = this;

        if (value) {
            this.timeout = setTimeout(function () {
                fetch('/items/autocomplete/' + value, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(function(results) {
                    self.autocompleteElement.setMenuItems(results);
                    self.autocompleteElement.open();
                })
            }, 500);
        }
    }

    onAutocomplete(item) {
        item = item[0] ? item[0] : null;


        if (!item) {
            return;
        }

        let existingItems = JSON.parse(this.formInputTarget.value);
        for (const object of this.items) {
            if (object.text === item) {
                item = object;
            }
        }

        let index = existingItems.indexOf(item.id);
        if (index === -1) {
            existingItems.push(item.id);
            this.resultTarget.insertAdjacentHTML('beforeend', this.getChip(item));
        }

        this.formInputTarget.value = JSON.stringify(existingItems);
        this.inputTarget.value = '';
    }

    getChip(item) {
        console.log(item)
        const thumbnail = item.image ? item.image : '/build/images/default.png';
        return '<tr class="related-item" data-id="' + item.id + '" data-text="' + item.text + '">' +
            '<td><img src="' + thumbnail + '"></td>' +
            '<td>' + item.text + '</td>' +
            '<td><i data-action="click->autocomplete--item#remove" class="fa fa-times close"></i></td>' +
        '</tr>';
    }
}
