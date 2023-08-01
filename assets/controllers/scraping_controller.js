import { Controller } from '@hotwired/stimulus';
import Sortable from "sortablejs";

export default class extends Controller {
    static targets = ['scrapedUrlInput', 'nameInput', 'imagePreview', 'imageInput', 'modalCloseButton', 'modalError', 'dataToScrapContainer']

    scrap(event) {
        event.preventDefault();
        let self = this;

        fetch('/scrapers/scrap', {
            method: 'POST',
            body: new FormData(event.target)
        })
            .then(response => response.json())
            .then(function(result) {
                if (typeof result === 'string') {
                    self.modalErrorTarget.innerHTML = result;
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
                console.log(result.html);
                self.dataToScrapContainerTarget.insertAdjacentHTML('beforeend', result.html);
            })
    }
}
