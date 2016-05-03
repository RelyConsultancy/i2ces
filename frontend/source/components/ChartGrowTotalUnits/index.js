import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'


// a factory function for the chart
const ChartGrowTotalUnits = (data) => {
  // format data
  const dates = data.chart.map(i => i.start_date)
  const exposed = data.chart.map(i => parseFloat(i.exposed))
  const control = data.chart.map(i => parseFloat(i.control))

  // below is a C3 chart
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
    regions: [
        {axis: 'x', start: data.timings[1].date_start, end: data.timings[1].date_end, class: 'campaign-period'},
    ]
  })

  return chart
}


// boilerplate for React component and dataset fetching
export default Component({
  getInitialState () {
    return { data: [] }
  },
  componentDidMount () {
    const { source } = this.props.component

    fetchDataset(source, (data) => {
      this.setState({ data })
    })
  },
  render () {
    const { data } = this.state
    
    console.log(data);
    
    if ('chart' in data) {
      return B({ className: style.chart }, ChartGrowTotalUnits(data))
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})