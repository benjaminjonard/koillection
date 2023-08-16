import { Controller } from '@hotwired/stimulus';
import Sortable from "sortablejs";

export default class extends Controller {
    static targets = ['datum', 'label', 'textsHolder', 'imagesHolder']

    index = null;
    boundInjectFields = null;
    currentTemplate = null;

    initialize() {
        this.boundInjectFields = this.injectFields.bind(this);
    }

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
            let holder = result.type === 'image' ? self.imagesHolderTarget : self.textsHolderTarget;
            let html = result.html.replace(/__placeholder__/g, self.index);
            html = html.replace(/__entity_placeholder__/g, self.element.dataset.entity);
            holder.insertAdjacentHTML('beforeend', html);
            self.index++;
            self.computePositions();
        })
    }

    addChoiceList(event) {
        let self = this;

        fetch('/datum/choice-list/' + event.target.dataset.id, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(function(result) {
            let html = result.html.replace(/__placeholder__/g, self.index);
            html = html.replace(/__entity_placeholder__/g, self.element.dataset.entity);
            self.textsHolderTarget.insertAdjacentHTML('beforeend', html);
            self.index++;
            self.computePositions();
        })
    }

    remove(event) {
        event.preventDefault();
        event.target.closest('.datum').remove();
        this.computePositions();
    }

    loadCollectionFields(event) {
        event.preventDefault();
        this.injectFields('/datum/load-collection-fields/' + event.target.dataset.collectionId);
    }

    loadCommonFields(event) {
        event.preventDefault();
        this.injectFields('/datum/load-common-fields/' + event.target.dataset.collectionId);
    }

    loadTemplateFields(event) {
        event.preventDefault();

        if (event.target.value !== this.currentTemplate) {
            this.currentTemplate = event.target.value;
            this.datumTargets.forEach((field) => {
                if (field.dataset.template) {
                    field.remove();
                }
            });

            if (event.target.value == '') {
                return;
            }
        }

        this.injectFields('/templates/' + event.target.value + '/fields');
    }

    injectFields(url) {
        let self = this;

        fetch(url, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(function(result) {
            self.injectData(result)
        })
    }

    injectNewScrapedData({ detail: { content } }) {
        this.injectData(content)
    }

    injectData(data) {
        data.forEach((datum) => {
            let existingDatum = null;
            let type, label, html;
            [type, label, html] = datum;

            this.labelTargets.forEach((input) => {
                if (input.value === label) {
                    existingDatum = input.closest('[data-additional-data-target="datum"]');
                }
            });

            if (existingDatum === null) {
                let holder = type == 'image' ? this.imagesHolderTarget : this.textsHolderTarget;
                html = html.replace(/__placeholder__/g, this.index);
                html = html.replace(/__entity_placeholder__/g, this.element.dataset.entity);
                holder.insertAdjacentHTML('beforeend', html);
                this.index++;
            } else if (existingDatum.querySelector('[data-additional-data-target="value"]').value === '') {
                let div = document.createElement('div');
                div.innerHTML = html.trim();
                existingDatum.querySelector('[data-additional-data-target="value"]').value = div.querySelector('[data-additional-data-target="value"]').value;
            }
        })

        this.computePositions();
    }
}
