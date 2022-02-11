import { Controller } from 'stimulus';
import { Tooltip } from '@materializecss/materialize';

export default class extends Controller {
    connect() {
        M.Tooltip.init(this.element, {
            enterDelay: 500,
            outDuration: 100
        });
    }
}
