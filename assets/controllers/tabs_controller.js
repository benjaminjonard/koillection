import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['tab', 'panel'];

    display(event) {
        this.tabTargets.forEach((element) => {
            element.classList.remove('current');
        })

        this.panelTargets.forEach((element) => {
            element.classList.remove('current');
            if (event.originalTarget.dataset.for === element.dataset.name) {
                event.originalTarget.classList.add('current');
                element.classList.add('current');
            }
        })
    }
}
