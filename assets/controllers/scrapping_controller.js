import { Controller } from '@hotwired/stimulus';
import Sortable from "sortablejs";

export default class extends Controller {
    static targets = ['nameInput', 'imagePreview', 'imageInput', 'modalCloseButton', 'modalError']

    scrap(event) {
        event.preventDefault();
        let self = this;

        fetch('/scrappers/scrap', {
            method: 'POST',
            body: new FormData(event.target)
        })
            .then(response => response.json())
            .then(function(result) {
                if (typeof result === 'string') {
                    self.modalErrorTarget.innerHTML = result;
                } else {
                    self.nameInputTarget.value = result.name;
                    self.imagePreviewTarget.src = result.image;
                    self.imageInputTarget.value = result.image;
                    self.dispatch("newScrappedData", { detail: { content: result.data } })
                    self.modalCloseButtonTarget.click();
                }
            })
    }
}
