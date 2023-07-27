import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['filename', 'input']

    displayFilename(event) {
        this.filenameTarget.innerHTML = event.target.files[0].name;
    }

    openUpload(event) {
        this.inputTarget.click();
    }
}
