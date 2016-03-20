import d3 from 'd3'
import c3 from 'c3'
import assign from 'object-assign'
import { Component, B } from '/components/component.js'
import { getUnique } from '/application/utils.js'
import d3_gantt from './d3_gantt.js'
import style from './style.css'
import palette from './palette.js'


export default Component({
  renderChart () {
    const { container } = this.refs
    const { type } = this.props

    switch (type) {
      case 'bar':
        const config = {
          bindto: container,
          color: { pattern: palette },
        }

        c3.generate(assign(config, this.props))
      break

      case 'gantt':
        const options = assign({}, this.props)

        if (!options.palette) {
          const types = getUnique(this.props.data.map(i => i.type))

          types.forEach((type, index) => {
            options.palette[type] = palette[index]
          })
        }

        const chart = d3.gantt(container, options)
      break
    }
  },
  componentDidMount () {
    this.renderChart()
  },
  render () {
    const { className } = this.props

    const attrs = {
      style: this.props.style,
      className: className || style.chart,
      ref: 'container',
    }
    return B(attrs)
  }
})