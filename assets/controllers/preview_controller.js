import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['image', 'input', 'deleteCheckbox'];

    static values = {
        defaultImage: String,
    }

    load(event) {
        let reader = new FileReader();
        let target = this.imageTarget;

        reader.onload = function (e) {
            target.src = e.target.result;
        };

        reader.readAsDataURL(event.target.files[0]);

        if (this.hasDeleteCheckboxTarget) {
            this.deleteCheckboxTarget.checked = false;
        }
    }

    remove() {
        this.inputTarget.value = '';
        this.imageTarget.src = this.defaultImageValue;
        this.deleteCheckboxTarget.checked = true;
    }
}
