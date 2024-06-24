import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";
import Sortable from "sortablejs";

export default class extends Controller {
    static targets = ['elementsContainer', 'input', 'element'];

    connect() {
        this.elementsContainerTarget.innerHTML = '';
        JSON.parse(this.inputTarget.value).forEach((value) => {
            this.elementsContainerTarget.insertAdjacentHTML('beforeend',  this.generateInput(value));
        });

        let self = this;
        let options = {
            draggable: '.datum-list-element',
            handle: '.handle',
            onSort: function () {
                self.updateInput()
            },
        }

        new Sortable(this.elementsContainerTarget, options);
    }

    addElement() {
        this.elementsContainerTarget.insertAdjacentHTML('beforeend',  this.generateInput(''));
        this.updateInput();
    }

    removeElement(event) {
        event.target.closest('.datum-list-element').remove();
        this.updateInput();
    }

    updateInput() {
        let values = [];

        this.elementTargets.forEach((element) => {
            values.push(element.value);
        });

        this.inputTarget.value = JSON.stringify(values);
    }

    generateInput(value) {
        value = value.replace(/"/g, '&quot;');

        let name = (Math.random() + 1).toString(36).substring(7);
        return '<div class="input-field outlined datum-list-element">' +
            `<input type="text" value="${value}" placeholder=" " id="` + name + '" data-datum-list-target="element" data-action="change->datum-list#updateInput">' +
            '<label for="' + name + '">' + Translator.trans('label.list_element_value') + '</label>' +
            '<i class="fa fa fa-arrows-v fa-fw handle"></i>' +
            '<i class="fa fa-remove fa-fw" data-action="click->datum-list#removeElement"></i>'
        '</div>';
    }
}
