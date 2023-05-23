import { Controller } from '@hotwired/stimulus';
import { M } from '@materializecss/materialize';

export default class extends Controller {
    connect() {
        M.Timepicker.init(this.element, {
            showClearBtn: true,
            twelveHour: false,
            container: 'html',
        });
    }
}
