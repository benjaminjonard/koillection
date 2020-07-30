let $filters = $('.js-filter-input');

$filters.val('');

let $elements = $('.element');

function normalize(str) {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "")
}

$filters.keyup( function() {
    let regex = new RegExp(normalize($(this).val()),'i');

    $elements.each(function() {
        if (normalize($(this).attr('data-title')).match(regex)) {
            $(this).removeClass('hidden');
        } else {
            $(this).addClass('hidden');
        }
    });
    var event = new Event('filter');
    window.dispatchEvent(event);
});

let $listElements = $('.list-element');
$filters.keyup( function() {
    let regex = new RegExp(normalize($(this).val()),'i');

    $listElements.each(function() {
        if (normalize($(this).attr('data-title')).match(regex)) {
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
    let $this = $(this);

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