import d3 from 'd3'
import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import style from './style.css'


export default Component({
  loadData () {
    const { source } = this.props.component

    fetchDataset(source, (data) => {
      this.setState({ data })
    })
  },
  getInitialState () {
    return {
      data: [],
    }
  },
  componentDidMount () {
    this.loadData()
  },
  render () {
    const { data } = this.state

    if (!data.length) return null

    const chart = Chart({
      type: 'bar',
      tooltip: { show: false },
      legend: { hide: true },
      data: {
        type: 'bar',
        x: 'labels',
        labels: { format: d3.format('1%') },
        columns: [
          ['labels'].concat(data.map(i => i.label)),
          ['values'].concat(data.map(i => i.value / 100)),
        ],
      },
      axis: {
        rotated: true,
        x: { type: 'category'},
        y: {
          tick: { format: d3.format('1%') },
        }
      },
      onMount (chart) {
        console.log(chart)
      }
    })

    const header = B({ className: style.header }, 'Reasons of non purchase')

    return B({ className: style.chart }, header, chart)
  }
})