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
    /*$('.has-preview').unbind('change');
    $('.has-preview').on('change', function(e) {
        var reader = new FileReader();
        var self = $(this);
        reader.onload = function (e) {
            self.closest('.row-file').find('img').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });*/
}

export function delay() {
    return (function(){
        let timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();
}

export function swipeDetection(el, callback){
    let touchsurface = el[0],
        swipedir,
        startX,
        startY,
        distX,
        distY,
        dist,
        threshold = 100, //required min distance traveled to be considered swipe
        restraint = 100, // maximum distance allowed at the same time in perpendicular direction
        allowedTime = 300, // maximum time allowed to travel that distance
        elapsedTime,
        startTime,
        handleswipe = callback || function(swipedir){}


    touchsurface.addEventListener('touchstart', function(e){
        let touchobj = e.changedTouches[0]
        swipedir = 'none'
        dist = 0
        startX = touchobj.pageX
        startY = touchobj.pageY
        startTime = new Date().getTime() // record time when finger first makes contact with surface
    }, false)

    touchsurface.addEventListener('touchend', function(e){
        let touchobj = e.changedTouches[0]
        distX = touchobj.pageX - startX // get horizontal dist traveled by finger while in contact with surface
        distY = touchobj.pageY - startY // get vertical dist traveled by finger while in contact with surface
        elapsedTime = new Date().getTime() - startTime // get time elapsed
        if (elapsedTime <= allowedTime){ // first condition for awipe met
            if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint){ // 2nd condition for horizontal swipe met
                swipedir = (distX < 0)? 'left' : 'right' // if dist traveled is negative, it indicates left swipe
            }
            else if (Math.abs(distY) >= threshold && Math.abs(distX) <= restraint){ // 2nd condition for vertical swipe met
                swipedir = (distY < 0)? 'up' : 'down' // if dist traveled is negative, it indicates up swipe
            }
        }
        handleswipe(swipedir)
    }, false)
}