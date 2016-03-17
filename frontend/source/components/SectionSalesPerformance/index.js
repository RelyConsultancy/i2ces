import { Component, B, Table, TBody, TR, TD } from '/components/component.js'
import Froala from '/components/Froala'
import ChartNV from '/components/ChartNV'
import { fetchDataset } from '/application/actions.js'
import { fmtUnit, fmtHTML } from '/application/utils.js'
import style from './style.css'


// data filters
const bySales = (item => item.metric == 'sales_growth')
const byShare = (item => item.metric == 'category_share')
const bySpend = (item => item.metric == 'average_spend')

const byCategory = (i => i.product.toLowerCase() == 'rest of cat')
const byBrand = (i => i.product.toLowerCase() == 'brand')
const byOffer = (i => i.product.toLowerCase() == 'offer')


const sortData = (data) => {
  let items = data.filter(byCategory)

  items.push(data.filter(byBrand).pop())
  items.push(data.filter(byOffer).pop())

  let competition = data
    .filter(i => items.indexOf(i) == -1)
    .sort((a, b) => (a.product > b.product))

  return items.concat(competition)
}


const SalesChart = ({ data }) => {
  data = sortData(data).map((item) => ({
    label: item.product,
    value: item.results,
  }))

  const chart = ChartNV({
    type: 'discrete_bar_chart',
    style: { height: '300px' },
    data: [{
      values: data,
      key: 'Sales Growth',
    }],
    format: value => fmtUnit(value * 100, 'percent'),
  })

  const label = B(
    { className: style.chart_label },
    'Period on period sales growth'
  )

  return B({ className: style.chart }, label, chart)
}


const Info = ({ component, isEditable, className, value }) => {
  const content = component[value] || ''

  if (isEditable) {
    return Froala({
      content,
      onChange: (e, editor) => {
        component[value] = editor.html.get()
      },
    })
  }
  // ignore empty strings
  else if (!content) {
    return null
  }
  else {
    return B({ className }, fmtHTML(content))
  }
}


const TableSales = ({ data }) => {
  const shares = sortData(data.filter(byShare))
  const spendings = sortData(data.filter(bySpend))
  const width = 100 / shares.length + '%'

  const table = Table(TBody(
    TR(...shares.map(i => TD(
      { style: { width } },
      fmtUnit(i.results * 100, 'percent')
    ))),
    TR(...spendings.map(i => TD(
      { style: { width } },
      fmtUnit(i.results, 'currency')
    )))
  ))

  const labels = B({ className: style.table_labels},
    B('Category share'),
    B('Avg. weekly sales')
  )

  return B({ className: style.table }, labels, table)
}


export default Component({
  loadData () {
    const { source } = this.props.component

    fetchDataset(source, (data) => {
      this.setState({ data })
    })
  },
  renderToggle () {
    const { editable, onSave } = this.props
    const { isEditable } = this.state
    const label = isEditable ? 'Save' : 'Edit'

    if (!editable) return null

    const onClick = () => {
      if (isEditable) onSave()
      this.setState({ isEditable: !isEditable })
    }

    return B({ onClick, className: style.toggle }, label)
  },
  getInitialState () {
    return {
      data: [],
      isEditable: false,
    }
  },
  componentDidMount () {
    this.loadData()
  },
  render () {
    const { component } = this.props
    const { isEditable, data } = this.state

    let content = B({ className: style.loading }, 'Loading data ...')

    if (data.length) {
      const info = Info({
        component,
        isEditable,
        value: 'info',
        className: style.info
      })

      const comment = Info({
        component,
        isEditable,
        value: 'comment',
        className: style.comment
      })

      const table = TableSales({ data })
      const chart = SalesChart({ data: data.filter(bySales) })

      content = B(info, chart, table, comment)
    }

    return B({ className: style.component }, content, this.renderToggle())
  }
})