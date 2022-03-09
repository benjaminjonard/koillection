import { Controller } from 'stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['input'];

    updateState(event) {
        event.preventDefault();

        let isHidden = event.target.classList.contains('fa-eye');
        if (isHidden) {
            this.inputTarget.type = 'text';
            event.target.classList.remove('fa-eye');
            event.target.classList.add('fa-eye-slash');
        } else {
            this.inputTarget.type = 'password';
            event.target.classList.remove('fa-eye-slash');
            event.target.classList.add('fa-eye');
        }
    }
}
