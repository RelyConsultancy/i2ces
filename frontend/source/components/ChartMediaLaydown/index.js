import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import { getUnique, capitalize } from '/application/utils.js'
import style from './style.css'


const colors = [
  '#2ECC71', // emerald
  '#3498DB', // peter river
  '#BDC3C7', // silver
  '#9B59B6', // amethyst
  '#34495E', // wet asphalt
  '#F1C40F', // sun flower
]


export default Component({
  render () {
    const { component } = this.props
    const palette = {}
    const types = getUnique(component.items.map(i => i.type)).sort()

    types.forEach((type, index) => {
      palette[type] = colors[index]
    })

    const chart = Chart({
      palette,
      type: 'gantt',
      data: component.items,
      tickFormat: '%d %b',
      style: { height: component.items.length * 3 + 'em' },
      className: style.chart,
    })

    const labels = B({ className: style.legend }, types.map((type, key) => {
      const color = B({ className: style.legend_color, style: {
        backgroundColor: colors[key]
      }})

      return B({ key, className: style.legend_label }, color, capitalize(type))
    }))

    return B({ className: style.component }, chart, labels)
  }
})