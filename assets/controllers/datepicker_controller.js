import { Controller } from '@hotwired/stimulus';
import { M } from '@materializecss/materialize';
import Translator from "bazinga-translator";

export default class extends Controller {
    connect() {
        M.Datepicker.init(this.element, {
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
            weekdaysAbbrev: [Translator.trans('global.days.sunday').substring(0, 1), Translator.trans('global.days.monday').substring(0, 1), Translator.trans('global.days.tuesday').substring(0, 1),
                Translator.trans('global.days.wednesday').substring(0, 1), Translator.trans('global.days.thursday').substring(0, 1), Translator.trans('global.days.friday').substring(0, 1),
                Translator.trans('global.days.saturday').substring(0, 1)],
            clear: Translator.trans('btn.clear'),
            close: Translator.trans('btn.close'),
            today: Translator.trans('global.today').substring(0, 3)+'.',
            format: document.getElementById('settings').dataset.dateFormat,
            container: '.main',
        });
    }
}
