import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        M.Timepicker.init(this.element, {
            showClearBtn: true,
            twelveHour: false,
            container: 'html',
        });
    }
}
