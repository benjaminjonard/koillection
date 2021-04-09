import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        let $itemAutocompleteInput = $(this.element).find('.js-autocomplete-input');
        let $itemAutocompleteFormInput = $(this.element).find('.js-autocomplete-form-input');
        let $itemAutocompleteResult = $(this.element).find('.js-autocomplete-result').find('tbody');
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

            if (val !== '') {
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

        $itemAutocompleteResult.on("click", ".close", function () {
            let existingItems = JSON.parse($itemAutocompleteFormInput.val());
            let index = existingItems.indexOf($(this).closest('.related-item').attr('data-id'));

            if (index > -1) {
                existingItems.splice(index, 1);
                $(this).closest('.related-item').remove();

            }

            $itemAutocompleteFormInput.val(JSON.stringify(existingItems));
        });

        if ($itemAutocompleteFormInput.length > 0) {
            let values = JSON.parse($itemAutocompleteFormInput.val());
            let currentItems = [];

            $.each(values, function (key, item) {
                let chip = '<tr class="related-item" data-id="' + item.id + '" data-text="' + item.name + '"><td><img src="' + item.thumbnail + '"></td><td>' + item.name + '</td><td><i class="fa fa-times close"></i></td></tr>';
                $itemAutocompleteResult.append(chip);
                currentItems.push(item.id);
            });

            $itemAutocompleteFormInput.val(JSON.stringify(currentItems));
        }

        function onItemAutocomplete(item) {
            let existingItems = JSON.parse($itemAutocompleteFormInput.val());

            $.each(items, function (key, object) {
                if (object.name === item) {
                    item = object;
                }
            });


            let index = existingItems.indexOf(item.id);
            if (index === -1) {
                existingItems.push(item.id);

                let chip = '<tr class="related-item" data-id="' + item.id + '" data-text="' + item.name + '"><td><img src="' + item.thumbnail + '"></td><td>' + item.name + '</td><td><i class="fa fa-times close"></i></td></tr>';
                $itemAutocompleteResult.append(chip);
            }

            $itemAutocompleteFormInput.val(JSON.stringify(existingItems));

            $itemAutocompleteInput.val('');
        }
    }
}
