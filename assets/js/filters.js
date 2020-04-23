$('#filter-by-name-input').val('');

var $elements = $('.element');
$('#filter-by-name-input').keyup( function() {
    var regex = new RegExp($(this).val(),'i');
    $elements.each(function() {
        if ($(this).attr('data-title').match(regex)) {
            $(this).removeClass('hidden');
        } else {
            $(this).addClass('hidden');
        }
    });
    var event = new Event('filter');
    window.dispatchEvent(event);
});

var $listElements = $('.list-element');
$('#filter-list-by-name-input').keyup( function() {
    var regex = new RegExp($(this).val(),'i');
    $listElements.each(function() {
        if ($(this).attr('data-title').match(regex)) {
            $(this).removeClass('hidden');
        } else {
            $(this).addClass('hidden');
        }
    });
});