import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";
import * as echarts from 'echarts/lib/echarts'
import { TooltipComponent } from 'echarts/components';
import { BarChart } from 'echarts/charts';
echarts.use([TooltipComponent, BarChart]);

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    chart = null;

    connect() {
        let data = JSON.parse(this.element.dataset.json);

        let primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color');
        let fontColor = getComputedStyle(document.documentElement).getPropertyValue('--font-color-main');

        this.chart = echarts.init(this.element);
        this.chart.setOption({
            tooltip: {
                formatter: function (params) {
                    return Translator.trans('statistics.items_added', params.value);
                }
            },
            color: primaryColor,
            xAxis: {
                type : 'category',
                data: data.map(element => element.abscissa),
                axisLabel: {
                    color: fontColor
                },
                axisTick: {
                    alignWithLabel: true,
                    lineStyle: {
                        color: fontColor
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: fontColor
                    }
                }
            },
            yAxis: {
                splitLine: {
                    lineStyle: {
                        color: fontColor
                    }
                },
                axisLabel: {
                    color: fontColor
                },
                axisTick: {
                    lineStyle: {
                        color: fontColor
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: fontColor
                    }
                }
            },
            series: [{
                type: 'bar',
                data: data.map(element => element.count)
            }]
        });
    }

    resize() {
        this.chart.resize();
    }
}
