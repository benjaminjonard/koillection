
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