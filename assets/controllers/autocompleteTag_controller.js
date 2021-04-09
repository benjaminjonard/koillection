import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'formInput', 'result']

    connect() {
        let $autocompleteInput = $(this.inputTarget);
        let $autocompleteFormInput = $(this.formInputTarget);
        let $autocompleteResult = $(this.resultTarget);
        let autocompleteResults = [];

        if ($autocompleteFormInput.length > 0) {
            $.each(JSON.parse($autocompleteFormInput.val()), function (key, item) {
                $autocompleteResult.append(getChip(item));
            });
        }

        let autocomplete = M.Autocomplete.init($autocompleteInput, {
            onAutocomplete: function (item) {
                onAutocomplete(item)
            }
        })[0];

        let timeout = null;
        $autocompleteInput.keyup(function (e) {
            autocomplete.updateData({});
            clearTimeout(timeout);
            let val = $(this).val();

            if (val !== '') {
                timeout = setTimeout(function () {
                    $.get('/tags/autocomplete/' + val, function (results) {
                        autocompleteResults = results;
                        let data = {};
                        $.each(results, function (key, result) {
                            data[result] = null;
                        });
                        autocomplete.updateData(data);
                        autocomplete.open();
                    }, "json");
                }, 500);
            }

            if (e.which === 13) {
                onAutocomplete($(this).val());
            }
        });

        $(this.element).on("click", ".close", function () {
            let existingTags = JSON.parse($autocompleteFormInput.val());

            let index = existingTags.indexOf($(this).parent('.chip').attr('data-id'));
            if (index > -1) {
                existingTags.splice(index, 1);
            }

            $autocompleteFormInput.val(JSON.stringify(existingTags));
        });

        function onAutocomplete(item) {
            let existingElements = JSON.parse($autocompleteFormInput.val());
            let id = item;

            let index = existingElements.indexOf(id);
            if (index === -1) {
                existingElements.push(id);
                $autocompleteResult.append(getChip(item));
            }

            $autocompleteFormInput.val(JSON.stringify(existingElements));
            $autocompleteInput.val('');
        }

        function getChip(label) {
            return '<div class="chip" data-id="' + label + '" data-text="' + label + '">' + label + '<i class="fa fa-times close"></i></div>'
        }
    }
}
