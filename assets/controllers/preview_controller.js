import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['image'];

    load(event) {
        let reader = new FileReader();
        let target = this.imageTarget;

        reader.onload = function (e) {
            target.src = e.target.result;
        };

        reader.readAsDataURL(event.target.files[0]);
    }
}
