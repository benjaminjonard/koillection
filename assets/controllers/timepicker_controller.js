import { Controller } from 'stimulus';
import { Timepicker } from 'materialize-css';

export default class extends Controller {
    connect() {
        M.Timepicker.init(this.element, {
            showClearBtn: true,
            twelveHour: false,
            container: 'html',
        });
    }
}
