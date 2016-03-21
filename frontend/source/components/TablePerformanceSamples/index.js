import { Component, B, Table, TBody, TR, TD } from '/components/component.js'
import { fetchDataset } from '/application/actions.js'
import { fmtNumber, fmtUnit, fmtDate } from '/application/utils.js'
import style from './style.css'


const TableSamples = ({ data }) => {
  const header = Table(TBody(TR(
    TD({ className: style.table_head }, 'SKU details'),
    TD({ className: style.table_head }, 'Sampled distributed during activity')
  )))

  const head = TR(
    { className: style.table_header },
    TD('SKU no.'),
    TD('SKU Name'),
    TD('Samples'),
    TD('Samples per store')
  )

  const rows = data.map((item) => TR(
    TD(item.sku),
    TD({ className: style.table_cell }, item.name),
    TD(fmtNumber(item.count)),
    TD(fmtNumber(item.per_store))
  ))

  const footer = TR(
    { className: style.table_footer },
    TD(''),
    TD('Total:'),
    TD(data.map(i => i.count).reduce((p, c) => fmtNumber(p + c))),
    TD(data.map(i => i.per_store).reduce((p, c) => fmtNumber(p + c)))
  )

  const table = Table(TBody(head, ...rows, footer))

  return B({ className: style.table_samples }, header, table)
}


const TableUplift = ({ data }) => {
  const header = Table(TBody(TR(
    TD({ className: style.table_head }, 'Incremental uplift during campaign')
  )))

  const head = TR(
    { className: style.table_header },
    TD('Unit'),
    TD('Unit %'),
    TD('Conversion from distributed samples')
  )

  const rows = data.map((item) => TR(
    { className: style.table_uplift_data },
    TD(fmtNumber(item.units)),
    TD(parseInt(item.units_percent).toLocaleString() + '%'),
    TD(parseInt(item.conversion).toLocaleString() + '%')
  ))

  const table = Table(TBody(head, ...rows))

  return B({ className: style.table_uplift }, header, table)
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
    const { data } = this.state

    if (!data) return null

    const samples = TableSamples({ data: data.sku_details })
    const uplift = TableUplift({ data: data.incremental_uplift })

    return B({ className: style.component }, samples, uplift)
  }
})