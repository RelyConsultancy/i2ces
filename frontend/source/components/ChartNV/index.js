import d3 from 'd3'
import nv from './nv.d3.js'
import { Component, SVG } from '/components/component.js'
import style from './style.css'


const MultiBarChart = ({ container, data, format }) => {
  const chart = nv.models.multiBarChart()
    // Specify the data accessors
    .x(item => item.label)
    .y(item => item.value)
    // Allow user to switch between 'Grouped' and 'Stacked' mode.
    .showControls(false)
    .reduceXTicks(false)
    .groupSpacing(0.2)

  chart.yAxis.tickFormat(d3.format('1%'))

  if (format) {
    const getValue = d3.format('')
    chart.valueFormat(value => format(getValue(value)))
  }

  d3.select(container)
    .datum(data)
    .call(chart)
    .selectAll('.nv-x .tick > text')
    .style({
      'font-size': '10px',
      'transform': 'translate(10px, 25px) rotate(90deg)',
    })

  nv.utils.windowResize(chart.update)
}


const DiscreteBarChart = ({ container, data, format }) => {
  const chart = nv.models.discreteBarChart()
    // Specify the data accessors
    .x(item => item.label)
    .y(item => item.value)
    // Too many bars and not enough room? Try staggering labels
    .staggerLabels(true)
    .showValues(true)

  // chart.xAxis.tickFormat(d3.format(',f'))
  chart.yAxis.tickFormat(d3.format('1%'))

  if (format) {
    const formatter = d3.format('')
    chart.valueFormat(value => format(formatter(value)))
  }

  d3.select(container)
    .datum(data)
    .call(chart)

  nv.utils.windowResize(chart.update)
}


export default Component({
  renderChart () {
    const { type, data, format } = this.props
    const { container } = this.refs

    switch (type) {
      case 'discrete_bar_chart':
        DiscreteBarChart({ container, data, format })
      break

      case 'multi_bar_chart':
        MultiBarChart({ container, data, format })
      break
    }
  },
  getInitialState () {
    return { isMounted: false }
  },
  componentDidMount () {
    this.setState({ isMounted: true })
  },
  render () {
    const { isMounted } = this.state
    const { style, className } = this.props

    const attrs = {
      className,
      style,
      ref: 'container',
    }
    return SVG(attrs, isMounted ? this.renderChart() : null)
  }
})