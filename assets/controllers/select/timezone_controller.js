import DefaultController from "./default_controller"

/* stimulusFetch: 'lazy' */
export default class extends DefaultController {
    connect() {
        if (typeof Intl !== 'undefined') {
            this.element.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
        }

        this.loadSelect();
    }
}
