import { Controller } from 'stimulus';
import Croppie from 'croppie'

export default class extends Controller {
    static targets = ['area', 'input', 'preview'];

    croppie = null;

    connect() {
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
        if (event.target.value === '') {
            return;
        }

        let form = this.element.querySelector('.file-input');
        let self = this;
        this.croppie.result({
            type: "canvas",
            size: { width: 200, height: 200 }
        })
        .then(function(imgBase64) {
            form.value = imgBase64;
            self.previewTarget.innerHTML = '<img src="' + imgBase64 + '">';
        });
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
        }
    }
}
