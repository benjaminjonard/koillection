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

        let primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color');
        let fontColor = getComputedStyle(document.documentElement).getPropertyValue('--font-color-main');
        let fontColorLight = getComputedStyle(document.documentElement).getPropertyValue('--font-color-light');


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
                        borderColor: primaryColor,
                        color: primaryColor
                    },
                    lineStyle: {
                        color: fontColorLight
                    },
                    label: {
                        color: fontColor
                    },
                    emphasis: {
                        focus: 'descendant'
                    }
                }
            ]
        });
    }

    resize() {
        this.chart.resize();
    }
}
