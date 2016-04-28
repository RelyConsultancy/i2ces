import d3 from 'd3'
// add SVGPathSeg polyfill as Chrome removed it (no longer part of specs)
import pathseg from 'pathseg'
import c3 from './c3.js'
import assign from 'object-assign'
import { Component, B } from '/components/component.js'
import { getUnique } from '/application/utils.js'
import d3_gantt from './d3_gantt.js'
import style from './style.css'
import palette from './palette.js'


export default Component({
  componentDidMount () {
    const { container } = this.refs
    const { type, onMount } = this.props

    switch (type) {
      case 'line':
      case 'bar':
        const config = {
          bindto: container,
          color: { pattern: palette },
        }

        var chart = c3.generate(assign(config, this.props))
      break

      case 'gantt':
        const options = assign({}, this.props)

        if (!options.palette) {
          const types = getUnique(this.props.data.map(i => i.type))

          types.forEach((type, index) => {
            options.palette[type] = palette[index]
          })
        }

        var chart = d3.gantt(container, options)
      break
    }

    if (onMount) onMount(chart)
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