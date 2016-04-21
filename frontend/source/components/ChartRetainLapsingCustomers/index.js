import { Component, B, Element } from '/components/component.js'
import Grid from '/components/Grid'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'
import numeral from 'numeral'

const H3 = Element('h3')
// a factory function for the chart
const ChartRetainLapsingCustomers = (data, type) => {
  
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
        'Exposed': '#7D3471'
      },
      columns: [
        ['Control'].concat(charts[type].control),
        ['Exposed'].concat(charts[type].exposed),
        ['Labels', 'During', 'Post']
      ],
      labels: {
          format: (value) => {
              return value
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
          text: 'Number of lapsed customers',
          position: 'outer-middle'
        },
        tick: {
            format: (value) => {
                return value
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
        
        console.log(data);
        
        return Grid({
          blocks: 2,
          items: [
              B(
                H3({ className: 'i2c-chart-title' }, data.charts.offer.exposed[0] - data.charts.offer.control[0] + ' additional lapsed customer purchasing offer during campaign'),
                B({ className: style.chart }, ChartRetainLapsingCustomers(data))
              ),
              B(
                H3({ className: 'i2c-chart-title' }, data.charts.brand.exposed[0] - data.charts.brand.control[0] + ' additional lapsed customer purchasing brand during campaign'),
                B({ className: style.chart }, ChartRetainLapsingCustomers(data, 'brand'))
              )
          ]
      })
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})