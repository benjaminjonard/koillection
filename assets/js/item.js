import * as utils from './utils'
import * as select from './select'

//Init sortable
if ($('#data').length > 0) {
    utils.reloadSortableList($('#data'), '.datum');
}

if ($('#item-images').length > 0) {
    utils.reloadSortableList($('#item-images'), '.datum');
}

var lastIndex = $('.datum').length;

function removeTemplateData()
{
    if ($('.datum[data-template]').length) {
        $('.datum[data-template]').remove();
    }
}

$('.selectTemplate').change( function() {
    if ( $(this).val() != '' ) {
        $('#data').html();
        $.get('/templates/' + $(this).val() + '/fields', function( result ) {
            removeTemplateData();
            $.each( result.fields, function( label, field ) {
                if ($('.itemLabel :input[value="'+ label +'"]').length == 0) {
                    if (field.type == 'image' || field.type == 'sign') {
                        var $holder = $('#item-images');
                    } else {
                        var $holder = $('#data');
                    }
                    $holder.append(field.html.replace(/__placeholder__/g, lastIndex).replace(/__entity_placeholder__/g, $('.js-data-actions').data('entity')));
                    $holder.find('.datum:last').find('.countable').characterCounter();
                    lastIndex++;
                }
            });
            select.loadSelect2Countries();
            utils.computePositions($('#data'));
            utils.computePositions($('#item-images'));
        }, "json" );
    } else {
        removeTemplateData();
    }
});

$('.js-btn-common-fields').click( function(e) {
    e.preventDefault();
    $.get('/datum/load-common-fields/' + $(this).attr('data-collection-id'), function( result ) {
        $.each( result.fields, function( label, field ) {
            if ($('.itemLabel :input[value="'+ label +'"]').length == 0) {
                if (field.type == 'image' || field.type == 'sign') {
                    var $holder = $('#item-images');
                } else {
                    var $holder = $('#data');
                }
                $holder.append(field.html.replace(/__placeholder__/g, lastIndex).replace(/__entity_placeholder__/g, $('.js-data-actions').data('entity')));
                $holder.find('.datum:last').find('.countable').characterCounter();
                lastIndex++;
            }
        });
        select.loadSelect2Countries();
        utils.computePositions($('#data'));
        utils.computePositions($('#item-images'));
    });
});

$('.js-btn-collection-fields').click( function(e) {
    e.preventDefault();
    $.get('/datum/load-collection-fields/' + $(this).attr('data-collection-id'), function( result ) {
        $.each( result.fields, function( label, field ) {
            if ($('.itemLabel :input[value="'+ label +'"]').length == 0) {
                if (field.type == 'image' || field.type == 'sign') {
                    var $holder = $('#item-images');
                } else {
                    var $holder = $('#data');
                }

                $holder.append(field.html.replace(/__placeholder__/g, lastIndex).replace(/__entity_placeholder__/g, $('.js-data-actions').data('entity')));
                $holder.find('.datum:last').find('.countable').characterCounter();
                lastIndex++;
            }
        });
        select.loadSelect2Countries();
        utils.computePositions($('#data'));
        utils.computePositions($('#item-images'));
    });
});

$( "#data" ).on("change", ".file-path", function(e) {
    let filename = e.target.files[0].name;
    $(this).closest('.file-field').find('.datum-original-filename').html(filename);
})

$('.js-add-field-btn').click( function() {
    $.get('/datum/' + $(this).data('type'), function( result ) {
        if (result.type == 'image' || result.type == 'sign') {
            var $holder = $('#item-images');
        } else {
            var $holder = $('#data');
        }
        $holder.append(result.html.replace(/__placeholder__/g, lastIndex).replace(/__entity_placeholder__/g, $('.js-data-actions').data('entity')));
        let $datum = $holder.find('.datum:last');
        $datum.find('.countable').characterCounter();
        $datum.find('.position').val($('#data').find('.datum').length);
        lastIndex++;
        select.loadSelect2Countries();
        utils.reloadSortableList($holder, '.datum');
        utils.computePositions($holder);
        utils.loadFilePreviews();
    });
});

$('#additionnalFields').on( "click", ".removeDatum", function() {
    $(this).closest('.datum').remove();
    utils.computePositions($(this).closest('.data-holder'));
});

var tagAutocomplete = M.Autocomplete.init(document.querySelectorAll('.autocomplete'), {
    onAutocomplete: function (item) {
        onAutocomplete(item);
    }
})[0];

let timeout = null;
$('#tagsAutocomplete').keyup(function (e) {
    tagAutocomplete.updateData({});
    clearTimeout(timeout);
    let val = $(this).val();

    if (val != '') {
        timeout = setTimeout(function() {
            $.get('/tags/autocomplete/' + val, function( result ) {
                let data = {};
                $.each(result, function( key, tag ) {
                    data[tag] = null;
                });

                tagAutocomplete.updateData(data);
                tagAutocomplete.open();
            }, "json" );
        }, 500);
    }

    if (e.which === 13) {
        onAutocomplete($(this).val());
    }
});

$( ".autocomplete-wrapper" ).on("click", ".close", function() {
    var tagsHolder = $('.tags-holder').first();
    var existingTags = JSON.parse(tagsHolder.val());

    var index = existingTags.indexOf($(this).parent('.chip').attr('data-id'));
    if (index > -1) {
        existingTags.splice(index, 1);
    }
    console.log(existingTags);

    tagsHolder.val(JSON.stringify(existingTags));
});

var $tagsHolder = $('.tags-holder').first();
if ($tagsHolder.length > 0) {
    $.each(JSON.parse($tagsHolder.val()), function( key, tag ) {
        var chip = '<div class="chip" data-id="' + tag + '" data-text="' + tag + '">' + tag + '<i class="fa fa-times close"></i></div>';
        $('.ac-tags').append(chip);
    });
}

function onAutocomplete(item)
{
    var tagsHolder = $('.tags-holder').first();
    var existingTags = JSON.parse(tagsHolder.val());

    var index = existingTags.indexOf(item);
    if (index == -1) {
        existingTags.push(item);

        var chip = '<div class="chip" data-id="' + item + '" data-text="' + item + '">' + item + '<i class="fa fa-times close"></i></div>';
        $('.ac-tags').append(chip);
    }

    tagsHolder.val(JSON.stringify(existingTags));

    $('#tagsAutocomplete').val('');
}


