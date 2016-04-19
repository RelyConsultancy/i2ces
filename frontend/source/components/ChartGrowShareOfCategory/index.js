import { Component, B } from '/components/component.js'
import Grid from '/components/Grid'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'


// a factory function for the chart
const ChartGrowShareOfCategory = (data, type) => {
  
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
        'Exposed': '#4FB7DA'
      },
      columns: [
        ['Control'].concat(charts[type].control),
        ['Exposed'].concat(charts[type].exposed),
        ['Labels', 'During', 'Post']
      ],
      labels: {
          format: (value) => {
              return value.toFixed(2) + '%'
          }
      }
    },
    axis: {
      x: {
        type: 'category',
        categories: ['During', 'Post']
      },
      y: {
        label: {
          text: 'Share of category',
          position: 'outer-middle'
        },
        tick: {
            format: (value) => {
                return value + '%'
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
              Grid({
                  blocks: 1,
                  items: [
                      B({className: 'i2c-chart-title'}, '<h3>Offer products share of category during and post campaign</h3>'),
                      B({ className: style.chart }, '<h3>Brand products share of category during and post campaign</h3>' + ChartGrowShareOfCategory(data))
                  ]
              }),
              Grid({
                  blocks: 1,
                  items: [
                      B({className: 'i2c-chart-title'}, '<h3>Brand products share of category during and post campaign</h3>'),
                      B({ className: style.chart }, ChartGrowShareOfCategory(data, 'brand'))
                  ]
              })
          ]
      })
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})