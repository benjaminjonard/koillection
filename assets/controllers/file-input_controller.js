import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['filename', 'input']

    displayFilename(event) {
        let filename = event.target.files[0].name;

        if (filename.length > 50) {
            filename = filename.substring(0, 50) + '...';
        }

        this.filenameTarget.innerHTML = filename;
    }

    openUpload(event) {
        this.inputTarget.click();
    }
}
