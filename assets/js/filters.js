let $filters = $('.js-filter-input');

$filters.val('');

var $elements = $('.element');
$filters.keyup( function() {
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
$filters.keyup( function() {
    var regex = new RegExp($(this).val(),'i');
    $listElements.each(function() {
        if ($(this).attr('data-title').match(regex)) {
            $(this).removeClass('hidden');
        } else {
            $(this).addClass('hidden');
        }
    });
});



let delay = (function(){
    let timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

//AJAX
let $ajaxFilters = $('.js-filter-input-ajax');
let $ajaxCheckboxes = $('.js-filter-checkbox-ajax');

$ajaxFilters.keyup(function() {
    $this = $(this);

    delay(function() {
        doAjaxCall(Object.entries($this.closest('form').serializeArray()));
    }, 250 );
});

$ajaxCheckboxes.change(function () {
    doAjaxCall(Object.entries($(this).closest('form').serializeArray()));
});

function doAjaxCall(params) {
    let url = window.location.href.split('?')[0];

    for (let [key, value] of params) {
        let symbol = key == 0 ? '?' : '&';
        url += symbol + value.name + '=' + value.value;
    }

    $.get(url, function(data) {
        $( ".js-ajax-table" ).replaceWith(data);
        window.history.pushState(null,"", url);
    });
}