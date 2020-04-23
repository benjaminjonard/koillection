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
        .done(function() {
            let $parents = $this.parents('.inventory-collection-show');
            console.log($parents);
            $.each( $parents, function() {
                let $counter = $(this).find('.js-checked-counter').first();
                if ($counter.length) {
                    $counter.html(parseInt($counter.html()) + add);
                }
            });
        })
        .fail(function() {

        });
});