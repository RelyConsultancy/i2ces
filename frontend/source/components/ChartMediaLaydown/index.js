import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import style from './style.css'


const legend = [{
  label: 'Evaluated media',
  color: '#2ecc71',
  key: 'media',
}, {
  label: 'Other',
  color: '#bdc3c7',
  key: 'other',
}, {
  label: 'In-Store promotions',
  color: '#3498db',
  key: 'promotion',
}]


export default Component({
  render () {
    const { component } = this.props
    const palette = {}

    legend.forEach((item) => {
      palette[item.key] = item.color
    })

    const chart = Chart({
      palette,
      type: 'gantt',
      data: component.items,
      tickFormat: '%d %b',
      style: { height: component.items.length * 3 + 'em' },
      className: style.chart,
    })

    const labels = B({ className: style.legend }, legend.map((item, key) => {
      const color = B({ className: style.legend_color, style: {
        backgroundColor: item.color
      }})

      return B({ key, className: style.legend_label }, color, item.label)
    }))

    return B({ className: style.component }, chart, labels)
  }
})