import Lightbox from "stimulus-lightbox"

export default class extends Lightbox {
    static targets = ['image'];

    connect() {
        super.connect()
        this.defaultOptions
        this.lightGallery
        let self = this;

        this.element.addEventListener('onAfterSlide', function(event){
            document.querySelectorAll('.js-custom-lightbox-button').forEach((element) => {
                element.remove();
            })

            if (self.imageTargets.length > 0) {
                if (self.imageTargets[event.detail.index].dataset.showUrl) {
                    let url = self.imageTargets[event.detail.index].dataset.showUrl;
                    self.injectShowButton(url);
                }

                if (self.imageTargets[event.detail.index].dataset.editUrl) {
                    let url = self.imageTargets[event.detail.index].dataset.editUrl;
                    self.injectEditButton(url);
                }

                if (self.imageTargets[event.detail.index].dataset.deleteId) {
                    let deleteId = self.imageTargets[event.detail.index].dataset.deleteId;
                    self.injectDeleteButton(deleteId);
                }
            }

        }, false);
    }

    get defaultOptions () {
        return {

        }
    }

    injectShowButton(url) {
        let html = `
            <a href="__url_placeholder__" class="button js-custom-lightbox-button lg-icon">
                <i class="fa fa-eye fa-fw"></i>
            </a>
        `;

        html = html.replace('__url_placeholder__', url);
        document.querySelector('.lg-toolbar').insertAdjacentHTML('beforeend', html);
    }

    injectEditButton(url) {
        let html = `
            <a href="__url_placeholder__" class="button js-custom-lightbox-button lg-icon">
                <i class="fa fa-pencil fa-fw"></i>
            </a>
        `;

        html = html.replace('__url_placeholder__', url);
        document.querySelector('.lg-toolbar').insertAdjacentHTML('beforeend', html);
    }

    injectDeleteButton(id) {
        let html = `
            <a href="#__id_placeholder__" class="modal-trigger button btn-grey js-custom-lightbox-button lg-icon">
                <i class="fa fa-trash fa-fw"></i>
            </a>
        `;

        html = html.replace('__id_placeholder__', id);
        document.querySelector('.lg-toolbar').insertAdjacentHTML('beforeend', html);
    }
}
