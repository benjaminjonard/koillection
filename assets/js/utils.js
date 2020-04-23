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
            computePositions($holder);
        }
    });
}

export function loadFilePreviews() {
    $('.has-preview').unbind('change');
    $('.has-preview').on('change', function(e) {
        var reader = new FileReader();
        var self = $(this);
        reader.onload = function (e) {
            self.closest('.row-file').find('img').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });
}
