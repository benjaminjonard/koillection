import { Controller } from '@hotwired/stimulus';
import Croppie from 'croppie'
import '../node_modules/croppie/croppie.css';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['area', 'input', 'emptyPreview', 'originalPreview', 'currentPreview', 'removeBtn', 'cancelBtn', 'deleteCheckbox'];

    croppie = null;

    connect() {
        this.initCroppie();
    }

    initCroppie() {
        let self = this;

        this.croppie = new Croppie(this.areaTarget, {
            viewport: { width: 150, height: 150, type: 'circle' },
            boundary: { width: 200, height: 200 },
            showZoomer: false,
            update: function (){
                self.areaTarget.dispatchEvent(new Event('mouseup'));
            }
        });

        /* Removes alt on preview image, causes a bug in Firefox */
        this.element.querySelector('.cr-image').alt = '';
        /* Add crosshair to cropper */
        this.element.querySelector('.cr-vp-circle').classList.add('fa', 'fa-plus', 'fa-fw');
    }

    loadImage(event) {
        this.readFile();
        this.areaTarget.dispatchEvent(new Event('mouseup'));
    }

    refreshImage(event) {
        let form = this.element.querySelector('.file-input');
        let self = this;
      
        this.croppie.result({
            type: "canvas",
            size: { width: 200, height: 200 }
        })
        .then(function(imgBase64) {
            form.value = imgBase64;
            self.currentPreviewTarget.src = imgBase64;
        });
    }

    async injectCroppie({ detail: { content } }) {
        let self = this;

        fetch(content)
            .then(res => res.blob())
            .then(function(blob) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    self.croppie.bind({
                        url : e.target.result,
                    });
                };
                reader.readAsDataURL(blob);
                self.deleteCheckboxTarget.checked = false;
            })
    }


    readFile() {
        let self = this;

        if (this.inputTarget.files && this.inputTarget.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                self.croppie.bind({
                    url : e.target.result,
                });
            };
            reader.readAsDataURL(this.inputTarget.files[0]);
            this.deleteCheckboxTarget.checked = false;
        }
    }

    remove() {
        this.element.querySelector('.file-input').value = '';
        this.inputTarget.value = '';
        this.deleteCheckboxTarget.checked = true;

        this.currentPreviewTarget.src = 'data:image/png;base64,' + this.emptyPreviewTarget.dataset.base64;
        this.croppie.destroy();
        this.initCroppie();
    }

    cancel() {
        this.element.querySelector('.file-input').value = '';
        this.inputTarget.value = '';
        this.deleteCheckboxTarget.checked = false;

        this.currentPreviewTarget.src = this.originalPreviewTarget.dataset.base64;
        this.croppie.destroy();
        this.initCroppie();
    }
}
