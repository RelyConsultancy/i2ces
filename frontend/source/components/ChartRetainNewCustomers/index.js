import { Component, B, Element, Table, TR, TD } from '/components/component.js'
import Grid from '/components/Grid'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'
import numeral from 'numeral'

const H3 = Element('h3')
// a factory function for the chart
const ChartRetainNewCustomers = (data, type) => {
  
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
              return numeral(value).format('0,0')
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
                return numeral(value).format('0,0')
            }
            
        }
      },
    },
  })

  return chart
}

const TableMediaCombos = (data) => {
    
    console.log(data);
    
    return Table(
              TR(
                TD('Channels and combinations'), TD('Number of households exposed'), TD('Control'), TD('During campaign uplift'), TD('% uplift vs control'))
            )
    
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
                H3({ className: 'i2c-chart-title' }, numeral(data.charts.offer[0].exposed - data.charts.offer[0].control).format('0,0') + ' new customers (trialists) returning to purchase offer product during campaign'),
                B({ className: style.chart }, ChartRetainNewCustomers(data))
              ),
              B(
                H3({ className: 'i2c-chart-title' }, 'During campaign uplift in new customers (trialists) returning, split by media channel combination'),
                B({ className: 'i2c-mc-table' }, TableMediaCombos(data))
              )
          ]
      })
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})

import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'


// a factory function for the chart
const ChartRetainNewCustomers = (data) => {
    
  // format data
  const labels = data.map(i => i.label)
  const uplift = data.map(i => i.uplift)

  // below is a C3 chart
  const chart = Chart({
    type: 'bar',
    data: {
      x: 'labels',
      columns: [
        ['labels'].concat(labels),
        ['uplift'].concat(uplift),
      ],
      names: {
        uplift: 'Units uplift',
        percent: 'Weekly unit uplift/HH vs average',
      },
    },
    axis: {
      x: {
        type: 'category'
      },
      y: {
        label: {
          text: 'lorem ipsum sit dolor',
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
      return B({ className: style.chart }, ChartRetainNewCustomers(data))
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})