import * as utils from './utils'


var lastIndex = $('.datum').length;

/*function removeTemplateData()
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
            //utils.computePositions($('#data'));
            //utils.computePositions($('#item-images'));
        }, "json" );
    } else {
        removeTemplateData();
    }
});*/

/*$('.js-btn-common-fields').click( function(e) {
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
        //utils.computePositions($('#data'));
        //utils.computePositions($('#item-images'));
    });
});*/

/*$('.js-btn-collection-fields').click( function(e) {
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
        //utils.computePositions($('#data'));
        //utils.computePositions($('#item-images'));
    });
});*/

/*$( "#data" ).on("change", ".file-path", function(e) {
    let filename = e.target.files[0].name;
    $(this).closest('.file-field').find('.datum-original-filename').html(filename);
})*/


