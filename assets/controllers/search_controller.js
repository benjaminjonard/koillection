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

            fetch(self.element.dataset.autocompleteUrl + encodeURIComponent(value), {
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
                        let url = "/search?search[term]=" + value;
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
        let characters = Array.from(content);
        let normalizedCharacters = characters.map((character) => this.normalize(character));
        let normalizedContent = normalizedCharacters.join('');
        let positions = [];
        normalizedCharacters.forEach((normalizedCharacter, index) => {
            for (let i = 0; i < normalizedCharacter.length; i++) {
                positions.push(index);
            }
        });

        let highlighted = characters.map(() => false);
        terms.split(' ').forEach((term) => {
            let normalizedTerm = this.normalize(term);
            if (normalizedTerm === '') {
                return;
            }

            let start = normalizedContent.indexOf(normalizedTerm);
            if (start === -1) {
                return;
            }

            let end = positions[start + normalizedTerm.length - 1];
            while (end + 1 < characters.length && normalizedCharacters[end + 1] === '') {
                end++;
            }

            for (let i = positions[start]; i <= end; i++) {
                highlighted[i] = true;
            }
        });

        let html = '';
        characters.forEach((character, index) => {
            if (highlighted[index] && (index === 0 || !highlighted[index - 1])) {
                html += '<b>';
            }

            html += this.escapeHtml(character);

            if (highlighted[index] && (index === characters.length - 1 || !highlighted[index + 1])) {
                html += '</b>';
            }
        });

        return html;
    }

    normalize(content) {
        return Array.from(content.normalize('NFKC'), (character) => this.fold(character))
            .join('')
            .replace(/\p{Default_Ignorable_Code_Point}/gu, '')
            .replace(/[ぁ-ゔゝゞ]/g, (character) => String.fromCharCode(character.charCodeAt(0) + 0x60))
            .normalize('NFD')
            .replace(/\p{Mn}+/gu, '')
            .normalize('NFC');
    }

    fold(character) {
        let candidates = [
            character,
            character.toLowerCase(),
            character.toUpperCase().toLowerCase(),
            character.toLowerCase().toUpperCase().toLowerCase(),
            character.toUpperCase(),
        ];

        return candidates.find((candidate) => !/\p{Changes_When_Casefolded}/u.test(candidate)) ?? character;
    }

    autocompleteResultFactory(label, url, type = null) {
        let li = this.htmlStringToDomElement('<li class="autocomplete-result"></li>');

        if (url) {
            let a = this.htmlStringToDomElement('<a></a>');
            a.href = url;
            a.innerHTML = label;
            li.appendChild(a);

            if (type) {
                let span = this.htmlStringToDomElement('<span></span>');
                span.innerHTML = ' (' + type + ')';
                a.appendChild(span);
            }
        } else {
            li.innerHTML = label;
        }

        return li;
    }

    escapeHtml(content) {
        let div = document.createElement('div');
        div.textContent = content;
        return div.innerHTML;
    }

    htmlStringToDomElement(html) {
        let template = document.createElement('template');
        html = html.trim();
        template.innerHTML = html;
        return template.content.firstChild;
    }
}
