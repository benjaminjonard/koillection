import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        M.Tooltip.init(this.element, {
            enterDelay: 500,
            outDuration: 100
        });
    }
}
