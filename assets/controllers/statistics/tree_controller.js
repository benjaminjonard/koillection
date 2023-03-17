import { Controller } from '@hotwired/stimulus';
import * as echarts from 'echarts/lib/echarts'
import { TooltipComponent } from 'echarts/components';
import { TreeChart } from 'echarts/charts';
echarts.use([TooltipComponent, TreeChart]);

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
                trigger: 'item',
                triggerOn: 'mousemove'
            },
            series: [
                {
                    type: 'tree',
                    data: [data],
                    layout: 'radial',
                    symbol: 'emptyCircle',
                    symbolSize: 7,
                    initialTreeDepth: -1,
                    animationDurationUpdate: 750,
                    itemStyle: {
                        borderColor: theme == 'dark' ? '#00ce99' : '#009688',
                    },
                    lineStyle: {
                        color: theme == 'dark' ? '#4a4b4d' : '#ccc'
                    },
                    label: {
                        color: theme == 'dark' ? '#a6a7a8' : '#555'
                    }
                }
            ]
        });
    }

    resize() {
        this.chart.resize();
    }
}
