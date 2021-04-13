import { Controller } from 'stimulus';

export default class extends Controller {
    display(event) {
        event.preventDefault();
        let open = !document.body.classList.contains('mobile-opened');
        if (open) {
            document.body.classList.add('mobile-opened');
        } else {
            document.body.classList.remove('mobile-opened');
        }
    }

    hide(event) {
        event.preventDefault();
        document.body.classList.remove('mobile-opened');
    }
}
