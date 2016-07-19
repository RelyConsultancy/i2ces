import { Component, B, Table, TR, TD } from '/components/component.js'
import { fetchDataset } from '/application/actions.js'
import { fmtUnit, fmtDate } from '/application/utils.js'
import style from './style.css'


const TableSales = ({ data }) => {
  const header = TR(
    { className: style.table_sales_header },
    TD('Category Sales'),
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

    if(!this.isUnmounted) {
        fetchDataset(source, (data) => {
          this.setState({ data })
        })
    }
  },
  getInitialState () {
    return {
      data: null,
    }
  },
  componentDidMount () {
    this.loadData()
  },
  componentWillUnmount () {
    this.isUnmounted = true
  },
  render () {
    const { data } = this.state

    if (!data) {
      return B({ className: style.loading }, 'Loading data ...')
    }

    return B({ className: style.component }, TableSales({ data }))
  }
})