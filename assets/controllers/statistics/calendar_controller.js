import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";
import * as echarts from 'echarts/lib/echarts'
import { TooltipComponent, CalendarComponent, VisualMapComponent } from 'echarts/components';
import { HeatmapChart } from 'echarts/charts';
echarts.use([TooltipComponent, CalendarComponent, VisualMapComponent, HeatmapChart]);

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    chart = null;

    monthsLabel = [
        Translator.trans('global.months.january'),
        Translator.trans('global.months.february'),
        Translator.trans('global.months.march'),
        Translator.trans('global.months.april'),
        Translator.trans('global.months.may'),
        Translator.trans('global.months.june'),
        Translator.trans('global.months.july'),
        Translator.trans('global.months.august'),
        Translator.trans('global.months.september'),
        Translator.trans('global.months.october'),
        Translator.trans('global.months.november'),
        Translator.trans('global.months.december')
    ];

    connect() {
        let json = JSON.parse(this.element.dataset.json);
        let year = this.element.dataset.year;
        let data = [];

        let primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color');
        let primaryDarkColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color-dark');
        let primaryLightColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color-light');
        let primaryLightestColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color-lightest');

        let backgroundColor = getComputedStyle(document.documentElement).getPropertyValue('--background-color');

        let fontColor = getComputedStyle(document.documentElement).getPropertyValue('--font-color-main');
        let fontColorLightest = getComputedStyle(document.documentElement).getPropertyValue('--font-color-lightest');

        let max = 0;
        Object.entries(json).forEach(([index, value]) => {
            data.push([value[0], value[1]]);
            if (value[1] > max) {
                max = value[1];
            }
        });

        let step = Math.floor(max/4);
        if (step === 0) {
            step = 1;
        }

        this.chart = echarts.init(this.element);
        this.chart.setOption({
            tooltip: {
                formatter: function (params) {
                    return Translator.trans('statistics.items_added', {count: params.value[1]});
                }
            },
            visualMap: {
                pieces: [
                    {min: (step*3) + 1, color: primaryDarkColor },
                    {min: (step*2) + 1, max: (step*3), color: primaryColor },
                    {min: (step*1) + 1, max: (step*2), color: primaryLightColor },
                    {min: 1, max: step, color: primaryLightestColor },
                    {min: 0, max: 0, color: fontColorLightest}
                ],
                type: 'piecewise',
                orient: 'horizontal',
                right: 215,
                bottom: 'bottom',
                textStyle: {
                    color: fontColor
                }
            },
            calendar: {
                splitLine: {
                    show: false,
                },
                top: 'middle',
                left: 'center',
                range: year,
                cellSize: 20,
                yearLabel: {show: false},
                itemStyle: {
                    borderWidth: 3,
                    borderColor: backgroundColor,
                    color: fontColorLightest
                },
                dayLabel: {
                    show: false
                },
                monthLabel: {
                    show: true,
                    nameMap: this.monthsLabel,
                    color: fontColor
                }
            },
            series: [
                {
                    type: 'heatmap',
                    coordinateSystem: 'calendar',
                    calendarIndex: 0,
                    data: data
                }
            ]
        });
    }

    resize() {
        this.chart.resize();
    }
}
