import { Component, B } from '/components/component.js'
import Grid from '/components/Grid'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'


// a factory function for the chart
const ChartAcquireNewCustomers = (data, type) => {
  
  type = type || 'offer'
  
  const charts = {}
  
  charts.offer = {
      exposed: data.charts.offer.map(i => i.exposed),
      control: data.charts.offer.map(i => i.control)
  }
  
  charts.brand = {
      exposed: data.charts.brand.map(i => i.exposed),
      control: data.charts.brand.map(i => i.control)
  }
  
  console.log(charts);
  // below is a C3 chart
  const chart = Chart({
    type: 'bar',
    data: {
      columns: [
        ['Control'].concat(charts[type].control),
        ['Exposed'].concat(charts[type].exposed)
      ]
    },
    axis: {
      x: {
        type: 'category',
        tick: {
            values: ['During', 'Post']
        }
      },
      y: {
        label: {
          text: 'New Customers',
          position: 'outer-middle',
        },
      },
    },
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

    if (data.length) {
      return Grid({
          blocks: 2,
          items: [
              B({ className: style.chart }, ChartAcquireNewCustomers(data)),
              B({ className: style.chart }, ChartAcquireNewCustomers(data, 'brand'))
          ]
      })
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})