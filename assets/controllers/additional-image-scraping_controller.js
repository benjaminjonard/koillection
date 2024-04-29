import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'urlInput', 'preview', 'imageInput'
    ]

    connect() {
        if (this.urlInputTarget.value) {
            this.previewTarget.src = this.urlInputTarget.value;
        }
    }
}
