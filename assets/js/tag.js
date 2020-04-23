$('.js-show-more-tags').click(function (e) {
    e.preventDefault();
    let $tagHolder = $(this).closest('.tags-block');
    $(this).addClass('hidden');
    $tagHolder.find('.js-show-less-tags').removeClass('hidden');
    $tagHolder.find('.tag.hidden').removeClass('hidden').addClass('js-to-be-hidden');
});

$('.js-show-less-tags').click(function (e) {
    e.preventDefault();
    let $tagHolder = $(this).closest('.tags-block');
    $tagHolder.find('.js-show-more-tags').removeClass('hidden');
    $(this).addClass('hidden');
    $(this).closest('.tags-block').find('.tag.js-to-be-hidden').removeClass('js-to-be-hidden').addClass('hidden');
});