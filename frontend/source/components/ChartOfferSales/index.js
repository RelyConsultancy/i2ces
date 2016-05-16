import d3 from 'd3'
import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import style from './style.css'


const ChartSales = ({ data, timings }) => {
  const dates = data.map(i => i.date_start)
  const exposed = data.map(i => parseFloat(i.exposed))
  const control = data.map(i => parseFloat(i.control))

  const chart = Chart({
    type: 'line',
    tooltip: { show: false },
    className: style.chart,
    padding: {
      top: 20,
      right: 10
    },
    data: {
      x: 'dates',
      columns: [
        ['dates'].concat(dates),
        ['Exposed'].concat(exposed),
        ['Control'].concat(control),
      ],
      colors: {
          Exposed: '#3F7CC0',
          Control: '#A6A6A6'
      },
    },
    axis: {
      x: {
        type: 'timeseries',
        tick: {
          format: '%d-%m-%Y',
          culling: false,
        },
      },
      y: {
        tick: {
          format: (value) => ('£' + (value.toFixed(0) / 1000) + 'k')
        },
      }
    },
    regions: [{
      axis: 'x',
      start: timings[1].date_start,
      end: timings[1].date_end,
      class: 'campaign-period'
    }]
  })

  const label = B({ className: style.chart_label }, 'Offer Sales')
  const overlay = B({ className: style.overlay});

  return B({ className: style.chart }, label, chart , overlay)
}


export default Component({
  loadData () {
    const { source } = this.props.component

    fetchDataset(source, (data) => {
      this.setState({ data })
    })
  },
  getInitialState () {
    return {
      data: null,
    }
  },
  componentDidMount () {
    this.loadData()
  },
  render () {
    const { component } = this.props
    const { data } = this.state

    if (!data) {
      return B({ className: style.loading }, 'Loading data ...')
    }

    const chart = ChartSales({
      data: data.chart,
      timings: data.timings,
    })

    return B({ className: style.component }, chart)
  }
})
