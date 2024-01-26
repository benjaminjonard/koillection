import lgVideo from '../node_modules/lightgallery/plugins/video';
import Lightbox from "stimulus-lightbox"
import '../node_modules/lightgallery/css/lightgallery-bundle.min.css';

/* stimulusFetch: 'lazy' */
export default class extends Lightbox {
    static targets = ['image'];

    connect() {
        super.connect()
        this.defaultOptions
        this.lightGallery
        let self = this;

        this.element.addEventListener('lgBeforeSlide', function(event){
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

                if (self.imageTargets[event.detail.index].dataset.deletePath) {
                    let path = self.imageTargets[event.detail.index].dataset.deletePath;
                    let message = self.imageTargets[event.detail.index].dataset.deleteMessage;
                    self.injectDeleteButton(path, message);
                }
            }

        }, false);
    }

    get defaultOptions () {
        return {
            selector: 'a',
            plugins: [lgVideo]
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

    injectDeleteButton(path, message) {
        let html = `
            <a href="#modal-delete" 
               class="modal-trigger button btn-grey js-custom-lightbox-button lg-icon"
               data-path="__path_placeholder__"
               data-message="__message_placeholder__"
            >
                <i class="fa fa-trash fa-fw"></i>
            </a>
        `;

        html = html.replace('__path_placeholder__', path);
        html = html.replace('__message_placeholder__', message);
        document.querySelector('.lg-toolbar').insertAdjacentHTML('beforeend', html);
    }
}
