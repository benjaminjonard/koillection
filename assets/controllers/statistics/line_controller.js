import { Controller } from '@hotwired/stimulus';
import Translator from "bazinga-translator";
import * as echarts from 'echarts/lib/echarts'
import { GridComponent, DataZoomComponent, TooltipComponent } from 'echarts/components';
import { LineChart } from 'echarts/charts';
echarts.use([GridComponent, DataZoomComponent, TooltipComponent, LineChart]);

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
                trigger: 'axis',
                position: function (params) {
                    return [params[0], '10%'];
                },
                formatter: function (params) {
                    return params[0].axisValue + ': ' + Translator.trans('statistics.items', {'count': params[0].data});
                }
            },
            color: primaryColor,
            xAxis: {
                type: 'category',
                data: Object.keys(data),
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
                type: 'value',
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
            dataZoom: [{
                handleIcon: 'path://M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
                handleSize: '80%',
                handleStyle: {
                    color: '#fff',
                    shadowBlur: 3,
                    shadowColor: 'rgba(0, 0, 0, 0.6)',
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                }
            }],
            series: [{
                data: Object.values(data),
                type: 'line',
                smooth: true,
                symbol: 'none',
                sampling: 'average',
                areaStyle: {
                    color: primaryColor
                }
            }]
        });
    }

    resize() {
        this.chart.resize();
    }
}
