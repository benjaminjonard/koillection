import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'formInput', 'result']

    $autocompleteInput = null;
    $autocompleteFormInput = null;
    $autocompleteResult = null;
    timeout = null;
    autocompleteElement = null;
    items = [];

    connect() {
        this.$autocompleteInput = $(this.inputTarget);
        this.$autocompleteFormInput = $(this.formInputTarget);
        this.$autocompleteResult = $(this.resultTarget);
        let self = this;

        this.autocompleteElement = M.Autocomplete.init(this.$autocompleteInput, {
            onAutocomplete: function (item) {
                self.onAutocomplete(item);
            }
        })[0];

        if (this.$autocompleteFormInput.length > 0) {
            let values = JSON.parse(this.$autocompleteFormInput.val());
            let currentItems = [];

            for (const item of values) {
                this.$autocompleteResult.append(this.getChip(item));
                currentItems.push(item.id);
            }

            this.$autocompleteFormInput.val(JSON.stringify(currentItems));
        }
    }

    remove(event) {
        let existingItems = JSON.parse(this.$autocompleteFormInput.val());
        let relatedItem = event.target.closest('.related-item');
        let index = existingItems.indexOf(relatedItem.dataset.id);

        if (index > -1) {
            existingItems.splice(index, 1);
            relatedItem.remove();
        }

        this.$autocompleteFormInput.val(JSON.stringify(existingItems));
    }

    autocomplete(event) {
        this.autocompleteElement.updateData({});
        clearTimeout(this.timeout);
        let val = this.$autocompleteInput.val();
        let self = this;

        if (val !== '') {
            this.timeout = setTimeout(function () {
                fetch('/items/autocomplete/' + val, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(function(results) {
                    self.items = results;
                    let data = {};
                    for (const result of results) {
                        data[result.name] = result.thumbnail;
                    }
                    self.autocompleteElement.updateData(data);
                    self.autocompleteElement.open();
                })
            }, 500);
        }

        if (event.which === 13) {
            this.onAutocomplete(val);
        }
    }

    onAutocomplete(item) {
        let existingItems = JSON.parse(this.$autocompleteFormInput.val());
        $.each(this.items, function (key, object) {
            if (object.name === item) {
                item = object;
            }
        });

        let index = existingItems.indexOf(item.id);
        if (index === -1) {
            existingItems.push(item.id);
            this.$autocompleteResult.append(this.getChip(item));
        }

        this.$autocompleteFormInput.val(JSON.stringify(existingItems));
        this.$autocompleteInput.val('');
    }

    getChip(item) {
        return '<tr class="related-item" data-id="' + item.id + '" data-text="' + item.name + '">' +
            '<td><img src="' + item.thumbnail + '"></td>' +
            '<td>' + item.name + '</td>' +
            '<td><i data-action="click->autocompleteItem#remove" class="fa fa-times close"></i></td>' +
        '</tr>';
    }
}
