import { Component, B, Element } from '/components/component.js'
import Grid from '/components/Grid'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'
import _ from 'underscore'

const H3 = Element('h3')
// a factory function for the chart
const ChartGrowFrequencyOfSharePerCustomer = (data, type) => {

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
              return value.toFixed(2)
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
                return value.toFixed(1)
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
                H3({ className: 'i2c-chart-title' }, 'Offer'),
                B({ className: style.chart }, ChartGrowFrequencyOfSharePerCustomer(data))
              ),
              B(
                H3({ className: 'i2c-chart-title' }, 'Brand'),
                B({ className: style.chart }, ChartGrowFrequencyOfSharePerCustomer(data, 'brand'))
              )
          ]
      })
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})