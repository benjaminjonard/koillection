function computePositions($holder) {
    $positions = $holder.find('.position');
    for (i = 0; i < $positions.length; i++) {
        $($positions[i]).val(i+1);
    }
}

function reloadSortableList($holder, elementSelector) {
    new Sortable(document.getElementById($holder.attr('id')), {
        draggable: elementSelector,
        handle: '.handle',
        onSort: function () {
            computePositions($holder);
        }
    });
}

function showAdditionalFieldsBlocks() {
    if ($('#data').find('.datum').length > 0) {
        $('#data').show();
    } else {
        $('#data').hide();
    }

    if ($('#item-images').find('.datum').length > 0) {
        $('#item-images').show();
    } else {
        $('#item-images').hide();
    }
}

//Init sortable
if ($('#data').length > 0) {
    reloadSortableList($('#data'), '.datum');
}

if ($('#item-images').length > 0) {
    reloadSortableList($('#item-images'), '.datum');
}

showAdditionalFieldsBlocks()
var lastIndex = $('.datum').length;

function removeTemplateData()
{
    if ($('.datum[data-template]').length) {
        $('.datum[data-template]').remove();
        showAdditionalFieldsBlocks()
    }
}

$('.selectTemplate').change( function() {
    if ( $(this).val() != '' ) {
        $('#data').html();
        $.get( Routing.generate('app_template_fields', {'id' : $(this).val() }), function( result ) {
            removeTemplateData();
            $.each( result.fields, function( label, field ) {
                if ($('.itemLabel :input[value="'+ label +'"]').length == 0) {
                    if (field.isImage) {
                        var $holder = $('#item-images');
                    } else {
                        var $holder = $('#data');
                    }
                    $holder.append(field.html.replace(/__placeholder__/g, lastIndex));
                    $holder.find('.datum:last').find('.countable').characterCounter();
                    lastIndex++;
                }
            });
            showAdditionalFieldsBlocks();
            computePositions($('#data'));
            computePositions($('#item-images'));
        }, "json" );
    } else {
        removeTemplateData();
    }
});

$('.btn-common-fields').click( function(e) {
    e.preventDefault();
    $.get( Routing.generate('app_datum_load_common_fields', {'id' : $(this).attr('data-collection-id') }), function( result ) {
        $.each( result.fields, function( label, field ) {
            if ($('.itemLabel :input[value="'+ label +'"]').length == 0) {
                if (field.isImage) {
                    var $holder = $('#item-images');
                } else {
                    var $holder = $('#data');
                }
                $holder.append(field.html.replace(/__placeholder__/g, lastIndex));
                $holder.find('.datum:last').find('.countable').characterCounter();
                lastIndex++;
            }
        });
        showAdditionalFieldsBlocks();
        computePositions($('#data'));
        computePositions($('#item-images'));
    });
});

$('.selectFieldType').change( function() {
    if ( $(this).val() != '' ) {
        $.get( Routing.generate('app_datum_get_html_by_type', {'type' : $(this).val() }), function( result ) {
            if (result.isImage) {
                var $holder = $('#item-images');
            } else {
                var $holder = $('#data');
            }
            $holder.append(result.html.replace(/__placeholder__/g, lastIndex));
            var $datum = $holder.find('.datum:last');
            $datum.find('.countable').characterCounter();
            $datum.find('.position').val($('#data').find('.datum').length);
            lastIndex++;
            showAdditionalFieldsBlocks()
            reloadSortableList($holder, '.datum');
            computePositions($holder);
            loadFilePreviews();
        });
    }
});

$('#additionnalFields').on( "click", ".removeDatum", function() {
    $(this).closest('.datum').remove();
    showAdditionalFieldsBlocks();
    computePositions($(this).closest('.data-holder'));
});

$(document).ready(function() {
    var tagsAutocomplete = $('#tagsAutocomplete').materialize_autocomplete({
        multiple: {
            enable: true,
            maxSize: Infinity,
            onExist: function (item) {
                Materialize.toast('Tag: ' + item.text + ' is already added!', 2000);
            },
            onAppend: function (item) {
                var tagsHolder = $('.tags-holder').first();
                var self = this;
                self.$el.removeClass('active');
                self.$el.click();
                var existingTags = JSON.parse(tagsHolder.val());

                var index = existingTags.indexOf(item.text);
                if (index == -1) {
                    existingTags.push(item.text);
                }

                tagsHolder.val(JSON.stringify(existingTags));
            },
            onRemove: function (item) {
                var tagsHolder = $('.tags-holder').first();
                var self = this;
                self.$el.removeClass('active');
                self.$el.click();

                var existingTags = JSON.parse(tagsHolder.val());
                var index = existingTags.indexOf(item.text);

                if (index > -1) {
                    existingTags.splice(index, 1);
                }
                tagsHolder.val(JSON.stringify(existingTags));
            }
        },
        appender: {
            el: '.ac-tags'
        },
        dropdown: {
            el: '#tagsDropdown'
        },
        allowNotSelectedItems: true,
        getData: function (value, callback) {
            $.get( Routing.generate('app_tag_autocomplete', {'search' : value }), function( result ) {
                var data = [];
                $.each(result, function( key, tag ) {
                    data[key] = {'id':tag, 'text':tag};
                });
                callback(value, data);
            }, "json" );
        }
    });

    var $tagsHolder = $('.tags-holder').first();
    if ($tagsHolder.length > 0) {
        $.each(JSON.parse($tagsHolder.val()), function( key, tag ) {
            tagsAutocomplete.append({'id':tag, 'text':tag});
        });
    }
});

