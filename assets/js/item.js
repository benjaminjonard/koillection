import * as utils from './utils'


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
    utils.reloadSortableList($('#data'), '.datum');
}

if ($('#item-images').length > 0) {
    utils.reloadSortableList($('#item-images'), '.datum');
}

showAdditionalFieldsBlocks();
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
        $.get('/templates/' + $(this).val() + '/fields', function( result ) {
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
            utils.computePositions($('#data'));
            utils.computePositions($('#item-images'));
        }, "json" );
    } else {
        removeTemplateData();
    }
});

$('.btn-common-fields').click( function(e) {
    e.preventDefault();
    $.get('/datum/load-common-fields/' + $(this).attr('data-collection-id'), function( result ) {
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
        utils.computePositions($('#data'));
        utils.computePositions($('#item-images'));
    });
});

$('.selectFieldType').change( function() {
    if ( $(this).val() != '' ) {
        $.get('/datum/' + $(this).val(), function( result ) {
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
            utils.reloadSortableList($holder, '.datum');
            utils.computePositions($holder);
            loadFilePreviews();
        });
    }
});

$('#additionnalFields').on( "click", ".removeDatum", function() {
    $(this).closest('.datum').remove();
    showAdditionalFieldsBlocks();
    utils.computePositions($(this).closest('.data-holder'));
});

var tagAutocomplete = M.Autocomplete.init(document.querySelectorAll('.autocomplete'), {
    onAutocomplete: function (item) {
        onAutocomplete(item);
    }
})[0];

$('#tagsAutocomplete').keyup(function (e) {
    tagAutocomplete.updateData({});
    if ($(this).val() != '') {

        $.get('/tags/autocomplete/' + $(this).val(), function( result ) {
            var data = {};
            $.each(result, function( key, tag ) {
                data[tag] = null;
            });

            tagAutocomplete.updateData(data);
            tagAutocomplete.open();
        }, "json" );
    }

    if(e.which == 13) {
        onAutocomplete($(this).val());
    }
});

$( ".autocomplete-wrapper" ).on("click", ".close", function() {
    var tagsHolder = $('.tags-holder').first();
    var existingTags = JSON.parse(tagsHolder.val());

    var index = existingTags.indexOf($(this).parent('.chip').attr('data-id'));
    console.log(existingTags);
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


