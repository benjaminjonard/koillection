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

        let theme = document.getElementById('settings').dataset.theme;
        if (theme == 'browser') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        this.chart = echarts.init(this.element);
        this.chart.setOption({
            tooltip: {
                formatter: function (params) {
                    return Translator.trans('statistics.items_added', params.value);
                }
            },
            color: [theme == 'dark' ? '#00ce99' : '#009688'],
            xAxis: {
                type : 'category',
                data: data.map(element => element.abscissa),
                axisLabel: {
                    color: theme == 'dark' ? '#f0f0f0': '#323233'
                },
                axisTick: {
                    alignWithLabel: true,
                    lineStyle: {
                        color: theme == 'dark' ? '#f0f0f0': '#323233'
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: theme == 'dark' ? '#f0f0f0': '#323233'
                    }
                }
            },
            yAxis: {
                splitLine: {
                    lineStyle: {
                        color: theme == 'dark' ? '#7d7f82': '#ccc'
                    }
                },
                axisLabel: {
                    color: theme == 'dark' ? '#f0f0f0': '#323233'
                },
                axisTick: {
                    lineStyle: {
                        color: theme == 'dark' ? '#f0f0f0': '#323233'
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: theme == 'dark' ? '#f0f0f0': '#323233'
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
