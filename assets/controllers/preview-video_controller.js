import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['image', 'video', 'source'];

    load(event) {
        this.sourceTarget.src = URL.createObjectURL(event.target.files[0]);
        this.videoTarget.load();

        this.imageTarget.classList.add('hidden');
        this.videoTarget.classList.remove('hidden');
    }
}
