import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['property'];

    displayProperties(event) {
        if (event.target.value == 'list') {
            this.propertyTargets.forEach((property) => {
                property.classList.remove('hidden')
            });
        } else {
            this.propertyTargets.forEach((property) => {
                property.classList.add('hidden')
            });
        }
    }
}
