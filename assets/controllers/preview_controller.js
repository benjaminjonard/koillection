import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['image'];

    load(event) {
        console.log('ij')
        let reader = new FileReader();
        let target = this.imageTarget;

        reader.onload = function (e) {
            target.src = e.target.result;
        };

        reader.readAsDataURL(event.originalTarget.files[0]);
    }
}
