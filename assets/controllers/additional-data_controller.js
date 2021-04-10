import { Controller } from 'stimulus';
import Sortable from "sortablejs";

export default class extends Controller {
    static targets = ['datum', 'textsHolder', 'imagesHolder']

    index = null;

    connect() {
        let self = this;
        this.index = this.datumTargets.length;
        this.computePositions();

        let options = {
            draggable: '.datum',
            handle: '.handle',
            onSort: function () {
                self.computePositions();
            }
        }

        if (this.hasTextsHolderTarget) {
            new Sortable(this.textsHolderTarget, options);
        }

        if (this.hasImagesHolderTarget) {
            new Sortable(this.imagesHolderTarget, options);
        }
    }

    computePositions() {
        if (this.hasTextsHolderTarget) {
            this.textsHolderTarget.querySelectorAll('.position').forEach((element, index) => {
                element.value = index+1;
            })
        }

        if (this.hasImagesHolderTarget) {
            this.imagesHolderTarget.querySelectorAll('.position').forEach((element, index) => {
                element.value = index+1;
            })
        }
    }

    add(event) {
        let self = this;
        fetch('/datum/' + event.target.dataset.type, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(function(result) {
            let holder = result.type == 'image' || result.type == 'sign' ? self.imagesHolderTarget : self.textsHolderTarget;
            let html = result.html.replace(/__placeholder__/g, self.index);
            html = html.replace(/__entity_placeholder__/g, self.element.dataset.entity);
            holder.insertAdjacentHTML('beforeend', html);
            self.index++;
            self.computePositions();
        })
    }

    remove(event) {
        event.preventDefault();
        event.target.closest('.datum').remove();
        this.computePositions();
    }
}
