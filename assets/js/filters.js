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

let $ajaxFilters = $('.js-filter-input-ajax');

let url = new URL(window.location.href);
let search = url.searchParams.get("search");
$ajaxFilters.val(search);
$ajaxFilters.keyup(function() {
    let $this = $(this);
    delay(function(){
        let url = window.location.href.split('?')[0] + '?search=' + $this.val();
        $.get(url, function(data) {
            $( ".js-tags-table" ).replaceWith(data);
            window.history.pushState(null,"", '?search=' + $this.val());
        });
    }, 200 );
});