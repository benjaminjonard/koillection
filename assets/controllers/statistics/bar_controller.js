import { Controller } from 'stimulus';
import Translator from "../../js/translator.min";
import * as echarts from 'echarts/lib/echarts'
import { TooltipComponent } from 'echarts/components';
import { BarChart } from 'echarts/charts';
echarts.use([TooltipComponent, BarChart]);

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    chart = null;
    isDarkMode =  document.getElementById('settings').dataset.theme === 'dark';

    connect() {
        let data = JSON.parse(this.element.dataset.json);

        this.chart = echarts.init(this.element);
        this.chart.setOption({
            tooltip: {
                formatter: function (params) {
                    return Translator.transChoice('statistics.items_added', params.value);
                }
            },
            color: [this.isDarkMode ? '#00ce99' : '#009688'],
            xAxis: {
                type : 'category',
                data: data.map(element => element.abscissa),
                axisLabel: {
                    color: this.isDarkMode ? '#f0f0f0': '#323233'
                },
                axisTick: {
                    alignWithLabel: true,
                    lineStyle: {
                        color: this.isDarkMode ? '#f0f0f0': '#323233'
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: this.isDarkMode ? '#f0f0f0': '#323233'
                    }
                }
            },
            yAxis: {
                splitLine: {
                    lineStyle: {
                        color: this.isDarkMode ? '#7d7f82': '#ccc'
                    }
                },
                axisLabel: {
                    color: this.isDarkMode ? '#f0f0f0': '#323233'
                },
                axisTick: {
                    lineStyle: {
                        color: this.isDarkMode ? '#f0f0f0': '#323233'
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: this.isDarkMode ? '#f0f0f0': '#323233'
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
