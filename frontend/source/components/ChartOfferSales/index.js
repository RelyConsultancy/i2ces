import d3 from 'd3'
import { Component, B, Table, TR, TD } from '/components/component.js'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import { fmtUnit, fmtDate } from '/application/utils.js'
import style from './style.css'


const ChartSales = ({ data }) => {
  const dates = data.map(i => i.start_date)
  const exposed = data.map(i => parseFloat(i.exposed))
  const control = data.map(i => parseFloat(i.control))

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
      colors: {
          Exposed: '#3F7CC0',
          Control: '#A6A6A6'
      },
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


const TableSales = ({ data }) => {
  const header = TR(
    { className: style.table_sales_header },
    TD('Offer Sales'),
    TD('Uplift'),
    TD('Percentage uplift')
  )

  const rows = [TR(
    TD('During'),
    TD(fmtUnit(data.during.uplift, 'currency')),
    TD(fmtUnit(data.during.percentage_uplift, 'percent'))
  )]

  if (data.post.uplift) {
    rows.push(TR(
      TD('Post'),
      TD(fmtUnit(data.post.uplift, 'currency')),
      TD(fmtUnit(data.post.percentage_uplift, 'percent'))
    ))
  }

  const footer = TR(
    { className: style.table_sales_footer },
    TD('Total'),
    TD(fmtUnit(data.total.uplift, 'currency')),
    TD(fmtUnit(data.total.percentage_uplift, 'percent'))
  )

  const table = Table(header, ...rows, footer)

  return B({ className: style.table_sales }, table)
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
      data: null,
    }
  },
  componentDidMount () {
    this.loadData()
  },
  render () {
    const { component } = this.props
    const { data } = this.state

    if (data) {
      return B(
        ChartSales({ data: data.chart }),
        TableSales({ data: data.table })
      )
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})