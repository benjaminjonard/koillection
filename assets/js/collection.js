$('.suggestion').on('click', function () {
    $(this).closest('.input-field').find('input').first().val($(this).data('suggestion'));
    $(this).closest('.input-field').find('label').first().addClass('active');
});