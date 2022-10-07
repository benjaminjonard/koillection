import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['typeInput', 'choiceList']

    connect() {
        this.displayChoiceList()
    }

    updateChoiceList(event) {
        event.preventDefault();
        this.displayChoiceList()
    }

    displayChoiceList()
    {
        if (this.typeInputTarget.value === 'list') {
            this.choiceListTarget.classList.remove("hidden");
        } else {
            this.choiceListTarget.classList.add("hidden");
        }
    }
}
