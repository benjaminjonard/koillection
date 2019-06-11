//Inventory creation page
let inventoryContentInput = $('#inventory_content');

function updateInventoryContentInput()
{
    let ids = [];
    $('.inventory-collection input[type="checkbox"]:checked').each(function() {
        if ($(this).data('id') !== 'all') {
            ids.push($(this).data('id'));
        }
    });

    inventoryContentInput.val(ids.join())
}

$('.inventory-collection input[type="checkbox"]').change(function() {
    let children = $(this).closest('p').siblings('ul').find('input[type="checkbox"]');
    let checked =  this.checked;

    children.each(function() {
        $(this).prop('checked', checked);
    });

    updateInventoryContentInput();
});

//Inventory show page
let inventoryId = $('#inventory-root').data('inventoryId');
$('.inventory-collection-show input[type="checkbox"]').change(function() {
    let data = {};
    let $this = $(this);
    let add = this.checked ? 1 : -1;

    data['items'] = {};
    data['items'][$(this).data('id')] = this.checked;

    $.post('/inventories/' + inventoryId  + '/check', data)
        .done(function(data) {
            $('.nav-pills').replaceWith(data.htmlForNavPills);
            let $parents = $this.parents('.inventory-collection-show');
            $.each( $parents, function() {
                let $counter = $(this).find('.js-checked-counter').first();

                //Update value
                let newValue = parseInt($counter.html()) + add;
                if ($counter.length) {
                    $counter.html(newValue);
                }

                //Update rate
                let $rate = $(this).find('.js-rate').first();
                let totalCounter = parseInt($(this).find('.js-total-counter').first().html());
                let newRate = (newValue*100)/totalCounter;
                $rate.html(Math.round(newRate*100) / 100);
            });
        })
        .fail(function() {

        });
});