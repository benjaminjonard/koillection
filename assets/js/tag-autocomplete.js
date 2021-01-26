export function init() {
    //Tag autocomplete
    let $tagAutocompleteDiv = $('.js-tag-autocomplete');
    let $tagAutocompleteInput = $tagAutocompleteDiv.find('.js-autocomplete-input');
    let $tagAutocompleteFormInput = $tagAutocompleteDiv.find('.js-autocomplete-form-input');
    let $tagAutocompleteResult = $tagAutocompleteDiv.find('.js-autocomplete-result');

    if ($tagAutocompleteFormInput.length > 0) {
        $.each(JSON.parse($tagAutocompleteFormInput.val()), function (key, tag) {
            var chip = '<div class="chip" data-id="' + tag + '" data-text="' + tag + '">' + tag + '<i class="fa fa-times close"></i></div>';
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

        if (val != '') {
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

    $tagAutocompleteDiv.on("click", ".close", function () {
        var existingTags = JSON.parse($tagAutocompleteFormInput.val());

        var index = existingTags.indexOf($(this).parent('.chip').attr('data-id'));
        if (index > -1) {
            existingTags.splice(index, 1);
        }

        $tagAutocompleteFormInput.val(JSON.stringify(existingTags));
    });

    function onTagAutocomplete(item) {
        var existingTags = JSON.parse($tagAutocompleteFormInput.val());

        var index = existingTags.indexOf(item);
        if (index == -1) {
            existingTags.push(item);

            var chip = '<div class="chip" data-id="' + item + '" data-text="' + item + '">' + item + '<i class="fa fa-times close"></i></div>';
            $tagAutocompleteResult.append(chip);
        }

        $tagAutocompleteFormInput.val(JSON.stringify(existingTags));

        $tagAutocompleteInput.val('');
    }
}