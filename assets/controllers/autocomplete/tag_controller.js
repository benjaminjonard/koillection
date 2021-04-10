import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'formInput', 'result']

    $autocompleteInput = null;
    $autocompleteFormInput = null;
    $autocompleteResult = null;
    timeout = null;
    autocompleteElement = null;

    connect() {
        this.$autocompleteInput = $(this.inputTarget);
        this.$autocompleteFormInput = $(this.formInputTarget);
        this.$autocompleteResult = $(this.resultTarget);
        let self = this;

        this.autocompleteElement = M.Autocomplete.init(this.$autocompleteInput, {
            onAutocomplete: function (item) {
                self.onAutocomplete(item)
            }
        })[0];

        if (this.$autocompleteFormInput.length > 0) {
            let values = JSON.parse(this.$autocompleteFormInput.val());
            for (const item of values) {
                this.$autocompleteResult.append(this.getChip(item));
            }
        }
    }

    remove(event) {
        let existingTags = JSON.parse(this.$autocompleteFormInput.val());
        let chip = event.target.closest('.chip');
        let index = existingTags.indexOf(chip.dataset.id);

        if (index > -1) {
            existingTags.splice(index, 1);
        }

        this.$autocompleteFormInput.val(JSON.stringify(existingTags));
    }

    autocomplete(event) {
        this.autocompleteElement.updateData({});
        clearTimeout(this.timeout);
        let val = this.$autocompleteInput.val();
        let self = this;

        if (val !== '') {
            this.timeout = setTimeout(function () {
                fetch('/tags/autocomplete/' + val, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(function(results) {
                    let data = {};
                    $.each(results, function (key, result) {
                        data[result] = null;
                    });
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
        let existingElements = JSON.parse(this.$autocompleteFormInput.val());
        let index = existingElements.indexOf(item);
        if (index === -1) {
            existingElements.push(item);
            this.$autocompleteResult.append(this.getChip(item));
        }

        this.$autocompleteFormInput.val(JSON.stringify(existingElements));
        this.$autocompleteInput.val('');
    }

    getChip(label) {
        return '<div class="chip" data-id="' + label + '" data-text="' + label + '">'
            + label + '<i data-action="click->autocompleteTag#remove" class="fa fa-times close"></i>' +
        '</div>';
    }
}
