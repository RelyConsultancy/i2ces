import d3 from 'd3'
import { Component, B, Table, TR, TD } from '/components/component.js'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import { fmtUnit } from '/application/utils.js'
import style from './style.css'
import _ from 'underscore'


// sort filters
const byCategory = (i => i.label.toLowerCase() == 'rest of cat')
const byBrand = (i => i.label.toLowerCase() == 'brand')
const byOffer = (i => i.label.toLowerCase() == 'offer')
const byAisle = (i => i.label.toLowerCase() == 'aisle')

const sortData = (data) => {
  
  
  const start_date = data.start_date;
  const data = data.table_data;
    
  let items = data.filter(byAisle)

  items.push(data.filter(byOffer).pop())
  items.push(data.filter(byBrand).pop())
  items.push(data.filter(byCategory).pop())
  
  let competition = data
    .filter(i => items.indexOf(i) == -1)
    .sort((a, b) => (a.label > b.label))
  
  return items.concat(competition)
}


const SalesChart = ({ table_data }) => {
    
  data = sortData(table_data)
  
  const chart = Chart({
    type: 'bar',
    tooltip: { show: false },
    legend: { hide: true },
    data: {
      type: 'bar',
      x: 'Labels',
      labels: { format: d3.format('1%') },
      columns: [
        ['Labels'].concat(data.map(i => i.label)),
        ['Results'].concat(data.map(i => i.value)),
      ],
      color (color, d) {
          return d.value < 0 ? '#ed7b29' : '#33bf6f';
      }
    },
    axis: {
      x: {
        type: 'category',
      },
      y: {
        tick: { format: d3.format('1%') },
      },
    },
    grid: {
      y: { show: false },
    },
    onMount (chart) {
        chart.ygrids.remove()
        chart.ygrids.add({value: 0, text: ''})
    },
    regions: [
        { axis: 'x', start: 0.5 , end: 2.5, class: 'region-offer-brand' },
        { axis: 'x', start: 3.5 , end: 3.5 + data.length - 4, class: 'region-competitors' }
    ]
    
  })

  const label = B(
    { className: style.chart_label },
    'Period on period sales growth'
  )

  return B({ className: style.chart }, label, chart)
}


// type filters
const bySales = (i => i.type == 'sales_growth')
const byShare = (i => i.type == 'category_share')
const bySpend = (i => i.type == 'average_spend')

const TableSales = ({ table_data }) => {
  const shares = sortData(table_data.filter(byShare))
  const spendings = sortData(table_data.filter(bySpend))
  const width = 100 / shares.length + '%'

  const table = Table(
    TR(...shares.map(i => TD(
      { style: { width } },
      (i.value * 100).toFixed(1) + '%'
    ))),
    TR(...spendings.map(i => TD(
      { style: { width } },
      fmtUnit(i.value, 'currency')
    )))
  )

  const labels = B({ className: style.table_labels},
    B('Category share'),
    B('Avg. weekly sales')
  )

  return B({ className: style.table }, labels, table)
}

const Timings = ({ start_date }) => {
    
    console.log(start_date);
    
    
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
      data: [],
    }
  },
  componentDidMount () {
    this.loadData()
  },
  render () {
    const { data } = this.state

    if (data.length) {
      return B(
        SalesChart({ data: data.filter(bySales) }),
        TableSales({ data }),
        Timings({ data })
      )
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})