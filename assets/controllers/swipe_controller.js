import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['next', 'previous'];

    connect() {
        let self = this;
        if (this.hasNextTarget || this.hasPreviousTarget) {
            this.swipe(document.getElementsByClassName('content-wrapper')[0], function(swipedir) {
                if (swipedir === 'left' && self.hasNextTarget) {
                    window.location = self.nextTarget.href;
                } else if (swipedir === 'right' && self.hasPreviousTarget) {
                    window.location = self.previousTarget.href;
                }
            })
        }
    }

    swipe(el, callback) {
        let touchsurface = el,
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
}
