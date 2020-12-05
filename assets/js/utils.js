import Sortable from 'sortablejs'
import Translator from "./translator.min";

export function initDatepickers()
{
    M.Datepicker.init(document.querySelectorAll('.datepicker'), {
        months: [Translator.trans('global.months.january'), Translator.trans('global.months.february'),
            Translator.trans('global.months.march'), Translator.trans('global.months.april'), Translator.trans('global.months.may'),
            Translator.trans('global.months.june'), Translator.trans('global.months.july'), Translator.trans('global.months.august'),
            Translator.trans('global.months.september'), Translator.trans('global.months.october'), Translator.trans('global.months.november'), Translator.trans('global.months.december')],
        monthsShort: [Translator.trans('global.months.january').substring(0, 3), Translator.trans('global.months.february').substring(0, 3),
            Translator.trans('global.months.march').substring(0, 3), Translator.trans('global.months.april').substring(0, 3), Translator.trans('global.months.may').substring(0, 3),
            Translator.trans('global.months.june').substring(0, 3), Translator.trans('global.months.july').substring(0, 3), Translator.trans('global.months.august').substring(0, 3),
            Translator.trans('global.months.september').substring(0, 3), Translator.trans('global.months.october').substring(0, 3), Translator.trans('global.months.november').substring(0, 3),
            Translator.trans('global.months.december').substring(0, 3)],
        weekdays: [Translator.trans('global.days.sunday'), Translator.trans('global.days.monday'), Translator.trans('global.days.tuesday'), Translator.trans('global.days.wednesday'),
            Translator.trans('global.days.thursday'), Translator.trans('global.days.friday'), Translator.trans('global.days.saturday')],
        weekdaysAbbrev: [Translator.trans('global.days.sunday').substring(0, 1), Translator.trans('global.days.monday').substring(0, 1), Translator.trans('global.days.tuesday').substring(0, 1),
            Translator.trans('global.days.wednesday').substring(0, 1), Translator.trans('global.days.thursday').substring(0, 1), Translator.trans('global.days.friday').substring(0, 1),
            Translator.trans('global.days.saturday').substring(0, 1)],
        clear: Translator.trans('btn.clear'),
        close: Translator.trans('btn.close'),
        today: Translator.trans('global.today').substring(0, 3)+'.',
        format: $('#js-date-format').data('jsDateFormat'),
        container: 'html',
    });
}

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