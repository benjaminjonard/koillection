import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";
import {useClickOutside} from "stimulus-use";

export default class extends Controller {
    static targets = ['resultsWrapper'];

    delay = (function(){
        let timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    connect() {
        useClickOutside(this)
    }

    open(event) {
        if (event.target.value.length > 2) {
            this.resultsWrapperTarget.classList.remove('hidden');
        }
    }

    clickOutside() {
        this.resultsWrapperTarget.classList.add('hidden');
    }

    search(event) {
        let self = this;
        let value = event.target.value;

        this.delay(function() {
            if (value.length < 2) {
                this.resultsWrapperTarget.classList.add('hidden');
                return;
            }

            fetch('/search/autocomplete/' + encodeURIComponent(value), {
                method: 'GET'
            })
                .then(response => response.json())
                .then(function(data) {
                    self.resultsWrapperTarget.classList.remove('hidden');
                    self.resultsWrapperTarget.innerHTML = '';
                    data.results.forEach((result) => {
                        let highlightedResult = self.highlight(result.label, value);
                        self.resultsWrapperTarget.appendChild(self.autocompleteResultFactory(highlightedResult, result.url, result.type));
                    });

                    if (data.totalResultsCounter > 5) {
                        let url = "/search?search[term]=" + value + "&search[searchInWishlists]&search[searchInAlbums]=&search[searchInCollections]=&search[searchInItems]=&search[searchInTags]=";
                        let label = Translator.trans('global.search.more_results', {'count' : data.totalResultsCounter - 5});
                        self.resultsWrapperTarget.appendChild(self.autocompleteResultFactory(label, url));
                    }

                    if (data.totalResultsCounter == 0) {
                        let url = null;
                        let label = Translator.trans('global.search.no_results');
                        self.resultsWrapperTarget.appendChild(self.autocompleteResultFactory(label, url));
                    }
                })
        }, 400);
    }

    highlight(content, terms) {
        terms = terms.split(' ');
        terms.forEach((term) => {
            let search = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
            search += '(?![^<]*>)'; //Prevent matching 'b' character inside the <b> tag
            let regex = new RegExp(search,'i');
            content = content.replace(regex, `<b>$&</b>`);
        });

        return content;
    }

    autocompleteResultFactory(label, url, type = null) {
        let li = this.htmlToElement('<li class="autocomplete-result"></li>');

        if (url) {
            let a = this.htmlToElement('<a></a>');
            a.href = url;
            a.innerHTML = label;
            li.appendChild(a);

            if (type) {
                let span = this.htmlToElement('<span></span>');
                span.innerHTML = ' (' + type + ')';
                a.appendChild(span);
            }
        } else {
            li.innerHTML = label;
        }

        return li;
    }

    htmlToElement(html) {
        let template = document.createElement('template');
        html = html.trim();
        template.innerHTML = html;
        return template.content.firstChild;
    }
}
