import * as utils from './utils'

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


