import { Controller } from '@hotwired/stimulus';
import Sortable from "sortablejs";

export default class extends Controller {
    static values = { url: String }

    connect() {
        let self = this;

        new Sortable(this.element, {
            draggable: '.wish',
            handle: '.handle',
            onSort: function () {
                self.save();
            }
        });
    }

    save() {
        let formData = new FormData();
        this.element.querySelectorAll('.wish').forEach((element) => {
            formData.append('wishes[]', element.dataset.id);
        });

        fetch(this.urlValue, {
            method: 'POST',
            body: formData
        });
    }
}
