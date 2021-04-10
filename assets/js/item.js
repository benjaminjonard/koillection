import * as utils from './utils'

//Init swiping
let next = $('#next');
let previous = $('#previous');
if (next.length > 0 || previous.length > 0) {
    utils.swipeDetection($('.content-wrapper'), function(swipedir) {
        if (swipedir === 'left' && next.length > 0) {
            window.location = next.attr('href');
        } else if (swipedir === 'right' && previous.length > 0) {
            window.location = previous.attr('href');
        }
    })
}

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
        utils.reloadSortableList($holder, '.datum');
        utils.computePositions($holder);
    });
});

$('#additionnalFields').on( "click", ".removeDatum", function() {
    $(this).closest('.datum').remove();
    utils.computePositions($(this).closest('.data-holder'));
});
