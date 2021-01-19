export function init() {
    //Tag autocomplete
    let $itemAutocompleteDiv = $('.js-item-autocomplete');
    let $itemAutocompleteInput = $itemAutocompleteDiv.find('.js-autocomplete-input');
    let $itemAutocompleteFormInput = $itemAutocompleteDiv.find('.js-autocomplete-form-input');
    let $itemAutocompleteResult = $itemAutocompleteDiv.find('.js-autocomplete-result');

    let items = [];

    let itemAutocomplete = M.Autocomplete.init($itemAutocompleteInput, {
        onAutocomplete: function (item) {
            onItemAutocomplete(item);
        }
    })[0];

    let timeout = null;
    $itemAutocompleteInput.keyup(function (e) {
        itemAutocomplete.updateData({});
        clearTimeout(timeout);
        let val = $(this).val();

        if (val != '') {
            timeout = setTimeout(function () {
                $.get('/items/autocomplete/' + val, function (result) {
                    items = result;
                    let data = {};
                    $.each(result, function (key, item) {
                        data[item.name] = item.thumbnail;
                    });

                    itemAutocomplete.updateData(data);
                    itemAutocomplete.open();
                }, "json");
            }, 500);
        }

        if (e.which === 13) {
            onItemAutocomplete($(this).val());
        }
    });

    $itemAutocompleteDiv.on("click", ".close", function () {
        var existingItems = JSON.parse($itemAutocompleteFormInput.val());

        var index = existingItems.indexOf($(this).parent('.related-item').attr('data-id'));
        if (index > -1) {
            existingItems.splice(index, 1);
            $(this).parent('.related-item').remove();
        }

        $itemAutocompleteFormInput.val(JSON.stringify(existingItems));
    });

    if ($itemAutocompleteFormInput.length > 0) {
        let values = JSON.parse($itemAutocompleteFormInput.val());
        let currentItems = [];

        $.each(values, function (key, item) {
            var chip = '<div class="related-item" data-id="' + item.id + '" data-text="' + item.name + '"><img src="' + item.thumbnail + '">' + item.name + '<i class="fa fa-times close"></i></div>';
            $itemAutocompleteResult.append(chip);
            currentItems.push(item.id);
        });

        $itemAutocompleteFormInput.val(JSON.stringify(currentItems));
    }

    function onItemAutocomplete(item) {
        var existingItems = JSON.parse($itemAutocompleteFormInput.val());

        $.each(items, function (key, object) {
            if (object.name == item) {
                item = object;
            }
        });


        var index = existingItems.indexOf(item.id);
        if (index == -1) {
            existingItems.push(item.id);

            var chip = '<div class="related-item" data-id="' + item.id + '" data-text="' + item.name + '"><img src="' + item.thumbnail + '">' + item.name + '<i class="fa fa-times close"></i></div>';
            $itemAutocompleteResult.append(chip);
        }

        $itemAutocompleteFormInput.val(JSON.stringify(existingItems));

        $itemAutocompleteInput.val('');
    }
}