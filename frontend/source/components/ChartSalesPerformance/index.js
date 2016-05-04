import moment from 'moment'
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
  
  let items = data.filter(byAisle)

  items.push(data.filter(byOffer).pop())
  items.push(data.filter(byBrand).pop())
  const rest_of_cat = data.filter(byCategory).pop()
  
  data.filter(byCategory).pop()
  
  let competition = data
    .filter(i => items.indexOf(i) == -1)
    .sort((a, b) => (a.label > b.label))
  
  competition = _.reject(competition, (i) => {
      return i.label.toLowerCase() == 'rest of cat'
  })
  
  
  
  return items.concat(competition, rest_of_cat);
}


const SalesChart = ({ data }) => {
  
  data = sortData(data)
  
  const max = () => {
        const val = _.max(data, (d) => {
            return d.value
        }).value
        
        return val <= 0 ? 0.1 : parseFloat(val) + 0.05
    }
  
  
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
        max: max()
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
        { axis: 'x', start: 0.5 , end: 2.5, class: 'region-offer-brand', label: 'Brand and offer', vertical: false, padding: 5 },
        { axis: 'x', start: 2.5 , end: 3.5 + data.length - 5, class: 'region-competitors', label: 'Competitor brands', vertical: false, padding: 5 }
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

const TableSales = ({ data }) => {
  const shares = sortData(data.filter(byShare))
  const spendings = sortData(data.filter(bySpend))
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

const Timings = ({ data }) => {
    
    const start_date = moment(data)
    console.log(start_date.format('DD/MM/YYYY'))
    const stages = [
        B({ className:style.stage }, 'Pre Period - ',  B({ className: style.stage_period }, start_date.clone().subtract(364, 'days').format('DD/MM/YYYY') + ' - ' + start_date.clone().subtract(183, 'days').format('DD/MM/YYYY'))),
        B({ className:style.stage }, 'Current Period - ',  B({ className: style.stage_period }, start_date.clone().subtract(182, 'days').format('DD/MM/YYYY') + ' - ' + start_date.clone().subtract(1, 'days').format('DD/MM/YYYY')))
    ]
    
    return B({ className: style.stages }, stages);
    
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
    
    console.log(data);
    
    if ('table_data' in data) {
      return B(
        SalesChart({ data: data.table_data.filter(bySales) }),
        TableSales({ data: data.table_data }),
        Timings({ data: data.start_date })
      )
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})