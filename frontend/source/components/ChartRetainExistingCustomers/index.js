import { Component, B, Element } from '/components/component.js'
import Grid from '/components/Grid'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'
import numeral from 'numeral'

const H3 = Element('h3')
// a factory function for the chart
const ChartRetainExistingCustomers = (data, type) => {
  
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
      type: 'bar',
      x: 'Labels',
      colors: {
        'Control': '#A6A6A6',
        'Exposed': '#CB0270'
      },
      columns: [
        ['Control'].concat(charts[type].control),
        ['Exposed'].concat(charts[type].exposed),
        ['Labels', 'During', 'Post']
      ],
      labels: {
          format: (value) => {
              return numeral(value).format('0.0a')
          }
      }
    },
    tooltip: {
        show: false
    },
    axis: {
      x: {
        type: 'category',
        categories: ['During', 'Post']
      },
      y: {
        label: {
          text: 'Frequency of Purchase',
          position: 'outer-middle'
        },
        tick: {
            format: (value) => {
                return numeral(value).format('0.0a')
            }
            
        }
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
    if ('charts' in data) {
        return Grid({
          blocks: 2,
          items: [
              B(
                H3({ className: 'i2c-chart-title' }, 'Frequency of purchase of offer during and post campaign'),
                B({ className: style.chart }, ChartRetainExistingCustomers(data))
              ),
              B(
                H3({ className: 'i2c-chart-title' }, 'Frequency of purchase of brand during and post campaign'),
                B({ className: style.chart }, ChartRetainExistingCustomers(data, 'brand'))
              )
          ]
      })
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})