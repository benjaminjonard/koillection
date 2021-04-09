import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'formInput', 'result']

    connect() {
        let $tagAutocompleteInput = $(this.inputTarget);
        let $tagAutocompleteFormInput = $(this.formInputTarget);
        let $tagAutocompleteResult = $(this.resultTarget);

        if ($tagAutocompleteFormInput.length > 0) {
            $.each(JSON.parse($tagAutocompleteFormInput.val()), function (key, tag) {
                let chip = '<div class="chip" data-id="' + tag + '" data-text="' + tag + '">' + tag + '<i class="fa fa-times close"></i></div>';
                $tagAutocompleteResult.append(chip);
            });
        }

        let tagAutocomplete = M.Autocomplete.init($tagAutocompleteInput, {
            onAutocomplete: function (item) {
                onTagAutocomplete(item);
            }
        })[0];

        let timeout = null;
        $tagAutocompleteInput.keyup(function (e) {
            tagAutocomplete.updateData({});
            clearTimeout(timeout);
            let val = $(this).val();

            if (val !== '') {
                timeout = setTimeout(function () {
                    $.get('/tags/autocomplete/' + val, function (result) {
                        let data = {};
                        $.each(result, function (key, tag) {
                            data[tag] = null;
                        });

                        tagAutocomplete.updateData(data);
                        tagAutocomplete.open();
                    }, "json");
                }, 500);
            }

            if (e.which === 13) {
                onTagAutocomplete($(this).val());
            }
        });

        $(this.element).on("click", ".close", function () {
            let existingTags = JSON.parse($tagAutocompleteFormInput.val());

            let index = existingTags.indexOf($(this).parent('.chip').attr('data-id'));
            if (index > -1) {
                existingTags.splice(index, 1);
            }

            $tagAutocompleteFormInput.val(JSON.stringify(existingTags));
        });

        function onTagAutocomplete(item) {
            let existingTags = JSON.parse($tagAutocompleteFormInput.val());

            let index = existingTags.indexOf(item);
            if (index === -1) {
                existingTags.push(item);

                let chip = '<div class="chip" data-id="' + item + '" data-text="' + item + '">' + item + '<i class="fa fa-times close"></i></div>';
                $tagAutocompleteResult.append(chip);
            }

            $tagAutocompleteFormInput.val(JSON.stringify(existingTags));

            $tagAutocompleteInput.val('');
        }
    }
}
