import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'formInput', 'result']

    timeout = null;
    autocompleteElement = null;

    connect() {
        let self = this;

        this.autocompleteElement = M.Autocomplete.init(this.inputTarget, {
            onAutocomplete: function (item) {
                self.onAutocomplete(item)
            }
        });

        if (this.formInputTarget.value.length > 0) {
            let values = JSON.parse(this.formInputTarget.value);
            for (const item of values) {
                this.resultTarget.insertAdjacentHTML('beforeend', this.getChip(item));
            }
        }
    }

    remove(event) {
        let existingTags = JSON.parse(this.formInputTarget.value);
        let chip = event.target.closest('.chip');
        let index = existingTags.indexOf(chip.dataset.id);

        if (index > -1) {
            existingTags.splice(index, 1);
        }

        this.formInputTarget.value = JSON.stringify(existingTags);
    }

    preventDefault(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    }

    autocomplete(event) {
        this.autocompleteElement.updateData({});
        clearTimeout(this.timeout);
        let value = this.inputTarget.value;
        let self = this;

        if (value) {
            this.timeout = setTimeout(function () {
                fetch('/tags/autocomplete/' + value, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(function(results) {
                    let data = {};
                    for (const result of results) {
                        data[result] = null;
                    }
                    self.autocompleteElement.updateData(data);
                    self.autocompleteElement.open();
                })
            }, 500);
        }

        if (value && event.which === 13) {
            this.onAutocomplete(value);
        }
    }

    onAutocomplete(item) {
        let existingElements = JSON.parse(this.formInputTarget.value);
        let index = existingElements.indexOf(item);
        if (index === -1) {
            existingElements.push(item);
            this.resultTarget.insertAdjacentHTML('beforeend', this.getChip(item));
        }

        this.formInputTarget.value = JSON.stringify(existingElements);
        this.inputTarget.value = '';
    }

    getChip(label) {
        return '<div class="chip" data-id="' + label + '" data-text="' + label + '">'
            + label + '<i data-action="click->autocomplete--tag#remove" class="fa fa-times close"></i>' +
        '</div>';
    }
}
