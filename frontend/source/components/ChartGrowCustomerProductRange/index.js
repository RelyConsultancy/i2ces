import { Component, B, Element } from '/components/component.js'
import Grid from '/components/Grid'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'
import _ from 'underscore'

const H3 = Element('h3')
// a factory function for the chart
const ChartGrowCustomerProductRange = (data, type, isPDF) => {
  
  type = type || 'offer'
  
  const charts = {}
  
  charts.offer = {
      exposed: _.sortBy(data.charts.offer, 'timeperiod').map(i => i.exposed),
      control: _.sortBy(data.charts.offer, 'timeperiod').map(i => i.control)
  }
  
  charts.brand = {
      exposed: _.sortBy(data.charts.brand, 'timeperiod').map(i => i.exposed),
      control: _.sortBy(data.charts.brand, 'timeperiod').map(i => i.control)
  }
  
  console.log(charts);

  // below is a C3 chart
  const chart = Chart({
    className: isPDF ? style.chart_pdf : style.chart, 
    type: 'bar',
    data: {
      type: 'bar',
      x: 'Labels',
      colors: {
        'Control': '#A6A6A6',
        'Exposed': '#C0CE0A'
      },
      columns: [
        ['Control'].concat(charts[type].control),
        ['Exposed'].concat(charts[type].exposed),
        ['Labels', 'During', 'Post']
      ],
      labels: {
          format: (value) => {
              return value.toFixed(0)
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
          text: 'Number of customers',
          position: 'outer-middle'
        },
        tick: {
            format: (value) => {
                return value.toFixed(0)
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
    const { isPDF } = this.props
    
    if ('charts' in data) {
        return B({ className: 'i2c-single-chart-middle' },
                H3({ className: isPDF ? 'i2c-chart-title-pdf' : 'i2c-chart-title' }, 'Offer'),
                B({ className: isPDF ? style.chart_pdf : style.chart }, ChartGrowCustomerProductRange(data, 'offer', isPDF))
                )
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})