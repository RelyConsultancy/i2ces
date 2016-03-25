import d3 from 'd3'
import { Component, B, Table, TBody, TR, TD } from '/components/component.js'
import Froala from '/components/Froala'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import { fmtUnit, fmtHTML } from '/application/utils.js'
import style from './style.css'


// sort filters
const byCategory = (i => i.label.toLowerCase() == 'rest of cat')
const byBrand = (i => i.label.toLowerCase() == 'brand')
const byOffer = (i => i.label.toLowerCase() == 'offer')

const sortData = (data) => {
  let items = data.filter(byCategory)

  items.push(data.filter(byBrand).pop())
  items.push(data.filter(byOffer).pop())

  let competition = data
    .filter(i => items.indexOf(i) == -1)
    .sort((a, b) => (a.label > b.label))

  return items.concat(competition)
}


const SalesChart = ({ data }) => {
  data = sortData(data)

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
      color: function (color, d) {
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
      y: { show: true },
    },
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

  const table = Table(TBody(
    TR(...shares.map(i => TD(
      { style: { width } },
      (i.value * 100).toFixed(1) + '%'
    ))),
    TR(...spendings.map(i => TD(
      { style: { width } },
      fmtUnit(i.value, 'currency')
    )))
  ))

  const labels = B({ className: style.table_labels},
    B('Category share'),
    B('Avg. weekly sales')
  )

  return B({ className: style.table }, labels, table)
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