import { Controller } from '@hotwired/stimulus';
import Sortable from "sortablejs";

export default class extends Controller {
    static targets = [
        'scrapedUrlInput', 'nameInput', 'urlInputContainer', 'fileInputContainer',
        'imagePreview', 'imageInput',
        'modalCloseButton', 'modalError',
        'dataToScrapContainer', 'form'
    ]

    scrap(event) {
        event.preventDefault();
        let self = this;
        self.modalErrorTarget.innerHTML = "";

        fetch('/scrapers/scrap', {
            method: 'POST',
            body: new FormData(event.target)
        })
            .then(response => response.json())
            .then(function(result) {
                if (typeof result === 'string') {
                    self.modalErrorTarget.innerHTML = result;
                } else {
                    if (result.form) {
                        self.formTarget.innerHTML = result.form;
                    } else {
                        if (result.name !== null) {
                            self.nameInputTarget.value = result.name;
                        }

                        if (self.hasImagePreviewTarget && result.image !== null) {
                            self.imagePreviewTarget.src = result.image;
                            self.imageInputTarget.value = result.image;
                        }

                        if (result.data !== null) {
                            self.dispatch("newScrapedData", { detail: { content: result.data } })
                            self.modalCloseButtonTarget.click();
                        }

                        self.scrapedUrlInputTarget.value = result.scrapedUrl;
                    }
                }
            })
    }

    loadDataPathCheckboxes(event) {
        let self = this;
        const id = event.target.value;
        self.dataToScrapContainerTarget.innerHTML = '';

        fetch('/scrapers/' + id + '/data-paths-checkboxes', {
            method: 'GET',
        })
            .then(response => response.json())
            .then(function(result) {
                self.dataToScrapContainerTarget.insertAdjacentHTML('beforeend', result.html);
            })
    }

    showFileInput(event) {
        event.preventDefault();
        this.urlInputContainerTarget.classList.add('hidden');
        this.urlInputContainerTarget.querySelector('input').value = null;

        this.fileInputContainerTarget.classList.remove('hidden');
    }

    showUrlInput(event) {
        event.preventDefault();
        this.fileInputContainerTarget.classList.add('hidden');
        this.fileInputContainerTarget.querySelector('input').value = null;

        this.urlInputContainerTarget.classList.remove('hidden');
    }
}
