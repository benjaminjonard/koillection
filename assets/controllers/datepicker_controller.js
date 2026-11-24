import {Controller} from '@hotwired/stimulus';
import Translator from "bazinga-translator";

import '../styles/modal.css'

export default class extends Controller {
    static targets = ['input'];

    open() {
        let self = this;
        let dialog = document.createElement('dialog');
        dialog.classList.add('modal', 'datepicker-modal');
        document.querySelector('.main').append(dialog);

        let contentWrapper = document.querySelector('.content-wrapper');
        if (contentWrapper !== null) {
            dialog.style.left = contentWrapper.getBoundingClientRect().left + 'px';
        }

        let datepicker = M.Datepicker.init(this.inputTarget, {
            i18n: {
                months: [Translator.trans('global.months.january'), Translator.trans('global.months.february'),
                    Translator.trans('global.months.march'), Translator.trans('global.months.april'), Translator.trans('global.months.may'),
                    Translator.trans('global.months.june'), Translator.trans('global.months.july'), Translator.trans('global.months.august'),
                    Translator.trans('global.months.september'), Translator.trans('global.months.october'), Translator.trans('global.months.november'), Translator.trans('global.months.december')],
                monthsShort: [Translator.trans('global.months.january').substring(0, 3), Translator.trans('global.months.february').substring(0, 3),
                    Translator.trans('global.months.march').substring(0, 3), Translator.trans('global.months.april').substring(0, 3), Translator.trans('global.months.may').substring(0, 3),
                    Translator.trans('global.months.june').substring(0, 3), Translator.trans('global.months.july').substring(0, 3), Translator.trans('global.months.august').substring(0, 3),
                    Translator.trans('global.months.september').substring(0, 3), Translator.trans('global.months.october').substring(0, 3), Translator.trans('global.months.november').substring(0, 3),
                    Translator.trans('global.months.december').substring(0, 3)],
                weekdays: [Translator.trans('global.days.sunday'), Translator.trans('global.days.monday'), Translator.trans('global.days.tuesday'), Translator.trans('global.days.wednesday'),
                    Translator.trans('global.days.thursday'), Translator.trans('global.days.friday'), Translator.trans('global.days.saturday')],
                weekdaysShort: [Translator.trans('global.days.sunday').substring(0, 3), Translator.trans('global.days.monday').substring(0, 3), Translator.trans('global.days.tuesday').substring(0, 3),
                    Translator.trans('global.days.wednesday').substring(0, 3), Translator.trans('global.days.thursday').substring(0, 3), Translator.trans('global.days.friday').substring(0, 3),
                    Translator.trans('global.days.saturday').substring(0, 3)],
                weekdaysAbbrev: [Translator.trans('global.days.sunday').substring(0, 1), Translator.trans('global.days.monday').substring(0, 1), Translator.trans('global.days.tuesday').substring(0, 1),
                    Translator.trans('global.days.wednesday').substring(0, 1), Translator.trans('global.days.thursday').substring(0, 1), Translator.trans('global.days.friday').substring(0, 1),
                    Translator.trans('global.days.saturday').substring(0, 1)],
                cancel: Translator.trans('btn.cancel'),
                done: Translator.trans('btn.ok'),
            },
            format: document.getElementById('settings').dataset.dateFormat,
            firstDay: parseInt(document.getElementById('settings').dataset.firstDayOfWeek),
            container: dialog,
            autoSubmit: false,
            yearRange: [1900, new Date().getFullYear() + 10],
            onConfirm: function () {
                let year = ("000" + this.date.getFullYear()).slice(-4);
                let month = ("0" + (this.date.getMonth() + 1)).slice(-2);
                let day = ("0" + this.date.getDate()).slice(-2);

                self.inputTarget.dataset.initialValue =  year + '-' + month + '-' + day

                dialog.close();
            },
            onCancel: function () {
                dialog.close();
            },
            onDraw: function () {
                this.yearTextEl.innerHTML = (this.date instanceof Date ? this.date : new Date()).getFullYear().toString();

                this.calendarEl.querySelectorAll('ul.select-dropdown').forEach(function (list) {
                    list.addEventListener('toggle', function (event) {
                        let selected = list.querySelector('li.selected');
                        if (event.newState !== 'open' || selected === null) {
                            return;
                        }

                        list.scrollTop = selected.offsetTop - (list.clientHeight - selected.offsetHeight) / 2;
                        selected.focus({preventScroll: true});
                    });
                });
            },
        });

        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) {
                dialog.close();
            }
        });

        dialog.addEventListener('close', function () {
            datepicker.destroy();
            dialog.remove();
        });

        let currentValue = this.inputTarget.dataset.initialValue;

        if (currentValue !== '') {
            datepicker.setDate(new Date(currentValue));
        }

        dialog.showModal();
    }
}
