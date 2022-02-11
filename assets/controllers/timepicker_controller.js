import { Controller } from 'stimulus';
import { Timepicker } from '@materializecss/materialize';

export default class extends Controller {
    connect() {
        M.Timepicker.init(this.element, {
            showClearBtn: true,
            twelveHour: false,
            container: 'html',
        });
    }
}
