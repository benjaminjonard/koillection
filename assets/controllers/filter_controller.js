import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['element'];

    filter(event) {
        let regex = new RegExp(this.normalize(event.target.value),'i');

        this.elementTargets.forEach((element) => {
            if (this.normalize(element.dataset.title).match(regex)) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        });

        window.dispatchEvent(new Event('filter'));
    }

    normalize(str) {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "")
    }
}
