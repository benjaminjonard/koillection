$('input[type=radio][name=theme]').change(function() {
    $.post( Routing.generate('app_settings_set_theme', {theme : $(this).val() }))
        .done(function( data ) {
            $('link:last').attr('href', data.theme)
        })
        .fail(function() {

        });
});

$('input[type=radio][name=locale]').change(function() {
    window.location.href = Routing.generate('app_settings_set_locale', {locale : $(this).val() });
});

$('input[type=radio][name=currency]').change(function() {
    $.post( Routing.generate('app_settings_set_currency', {currency : $(this).val() }))
        .done(function( data ) {

        })
        .fail(function() {

        });
});

$('input[type=radio][name=visibility]').change(function() {
    $.post( Routing.generate('app_settings_set_visibility', {visibility : $(this).val() }))
        .done(function( data ) {

        })
        .fail(function() {

        });
});
