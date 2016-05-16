import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import { getUnique } from '/application/utils.js'
import style from './style.css'


const colors = [
  '#2ECC71', // emerald
  '#3498DB', // peter river
  '#BDC3C7', // silver
  '#9B59B6', // amethyst
  '#34495E', // wet asphalt
  '#F1C40F', // sun flower
]


export default ({ component }) => {
  const { items } = component
  const types = getUnique(items.map(i => i.type)).sort()
  const labels = getUnique(items.map(i => i.label))
  const palette = {}

  types.forEach((type, index) => {
    palette[type] = colors[index]
  })

  const chart = Chart({
    palette,
    type: 'gantt',
    data: items,
    tickFormat: '%d %b',
    style: { height: (labels.length * 2 + 4) + 'em' },
    className: style.chart,
  })

  const legend = B({ className: style.legend }, types.map((type, key) => {
    const color = B({ className: style.legend_color, style: {
      backgroundColor: colors[key]
    }})

    const label = component.legend.filter(i => i.type == type).shift().label

    return B({ key, className: style.legend_label }, color, label)
  }))

  return B({ className: style.component }, chart, legend)
}