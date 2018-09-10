$('input[type=radio][name=theme]').change(function() {

    $.post('/settings/set-theme/' + $(this).val())
        .done(function( data ) {
            $('link:last').attr('href', data.theme)
        })
        .fail(function() {

        });
});

$('input[type=radio][name=locale]').change(function() {
    window.location.href = '/settings/set-locale/' + $(this).val();
});

$('input[type=radio][name=currency]').change(function() {
    $.post('/settings/set-currency/' + $(this).val())
        .done(function( data ) {

        })
        .fail(function() {

        });
});

$('input[type=radio][name=visibility]').change(function() {
    $.post('/settings/set-visibility/' + $(this).val())
        .done(function( data ) {

        })
        .fail(function() {

        });
});
