import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'formInput', 'result']

    connect() {
        let type = this.element.dataset.autocompleteType;
        let $autocompleteInput = $(this.inputTarget);
        let $autocompleteFormInput = $(this.formInputTarget);
        let $autocompleteResult = $(this.resultTarget);
        let autocompleteResults = [];

        if ($autocompleteFormInput.length > 0) {
            if (type === 'item') {
                let values = JSON.parse($autocompleteFormInput.val());
                let currentItems = [];

                $.each(values, function (key, item) {
                    $autocompleteResult.append(getChip(item));
                    currentItems.push(item.id);
                });

                $autocompleteFormInput.val(JSON.stringify(currentItems));
            } else {
                $.each(JSON.parse($autocompleteFormInput.val()), function (key, item) {
                    $autocompleteResult.append(getChip(item));
                });
            }
        }

        let autocomplete = M.Autocomplete.init($autocompleteInput, {
            onAutocomplete: function (item) {
                onAutocomplete(item, type)
            }
        })[0];

        let timeout = null;
        $autocompleteInput.keyup(function (e) {
            autocomplete.updateData({});
            clearTimeout(timeout);
            let val = $(this).val();

            if (val !== '') {
                timeout = setTimeout(function () {
                    console.log(type)
                    let url = type === 'item' ? '/items/autocomplete/' : '/tags/autocomplete/';
                    $.get(url + val, function (results) {
                        autocompleteResults = results;
                        let data = {};
                        $.each(results, function (key, result) {
                            if (type === 'item') {
                                data[result.name] = result.thumbnail;
                            } else {
                                data[result] = null;
                            }
                        });
                        autocomplete.updateData(data);
                        autocomplete.open();
                    }, "json");
                }, 500);
            }

            if (e.which === 13) {
                onAutocomplete($(this).val(), type);
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

        function onAutocomplete(item, type) {
            let existingElements = JSON.parse($autocompleteFormInput.val());
            let id = item;

            if (type === 'item') {
                $.each(autocompleteResults, function (key, object) {
                    if (object.name === item) {
                        item = object;
                        id = item.id;
                    }
                });
            }

            let index = existingElements.indexOf(id);
            if (index === -1) {
                existingElements.push(id);
                $autocompleteResult.append(getChip(item));
            }

            $autocompleteFormInput.val(JSON.stringify(existingElements));
            $autocompleteInput.val('');
        }

        function getChip(item) {
            if (item.id) {
                return '<tr class="related-item" data-id="' + item.id + '" data-text="' + item.name + '"><td><img src="' + item.thumbnail + '"></td><td>' + item.name + '</td><td><i class="fa fa-times close"></i></td></tr>';
            }

            return '<div class="chip" data-id="' + item + '" data-text="' + item + '">' + item + '<i class="fa fa-times close"></i></div>'
        }
    }
}
