import Sortable from 'sortablejs'

export function computePositions($holder) {
    let $positions = $holder.find('.position');
    for (let i = 0; i < $positions.length; i++) {
        $($positions[i]).val(i+1);
    }
}

export function reloadSortableList($holder, elementSelector) {
    new Sortable(document.getElementById($holder.attr('id')), {
        draggable: elementSelector,
        handle: '.handle',
        onSort: function () {
            utils.computePositions($holder);
        }
    });
}
