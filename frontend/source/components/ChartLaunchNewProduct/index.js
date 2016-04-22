import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'


// a factory function for the chart
const ChartLaunchNewProduct = (data) => {
  const dates = data.map(i => i.start_date)
  const exposed = data.map(i => parseFloat(i.exposed))
  const control = data.map(i => parseFloat(i.control))
  
  console.log(data);
  
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
  })

  const label = B({ className: style.chart_label }, 'Offer Sales')

  return B({ className: style.chart }, label, chart)
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
      
    console.log(this.state);
    
    const { data } = this.state
    
    console.log(chart);
    
    if (data.chart) {
      return B({ className: style.chart }, ChartLaunchNewProduct(data.chart))
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})