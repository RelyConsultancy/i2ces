import moment from 'moment'
import { Component, B, Table, TBody, TR, TD } from '/components/component.js'
import Froala from '/components/Froala'
import ChartNV from '/components/ChartNV'
import { fetchDataset } from '/application/actions.js'
import { fmtUnit, fmtHTML } from '/application/utils.js'
import style from './style.css'


const fmtDate = (date => moment(date, 'YYYY/MM/DD').format('DD/MM/YY'))
const byOffer = (i => i.product.toLowerCase() == 'offer')
const byBrand = (i => i.product.toLowerCase() == 'brand')
const byCompetitor = (i => i.product.toLowerCase() == 'competitor')
const byDate = (a, b) => (a.date < b.date ? -1 : 1)
const fmtChartData = (item) => ({
  label: fmtDate(item.date),
  value: parseFloat(item.results).toFixed(2),
})


const ActivityChart = ({ data }) => {
  const chart = ChartNV({
    type: 'multi_bar_chart',
    style: { height: '300px' },
    data: [{
      key: 'Offer',
      values: data.filter(byOffer).sort(byDate).map(fmtChartData)
    }, {
      key: 'Brand',
      values: data.filter(byBrand).sort(byDate).map(fmtChartData)
    }, {
      key: 'Competitor',
      values: data.filter(byCompetitor).sort(byDate).map(fmtChartData)
    }],
    // format: value => fmtUnit(value * 100, 'percent'),
  })

  const label = B(
    { className: style.chart_label },
    '% Products on promotion'
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

      const chart = ActivityChart({ data })

      content = B(info, chart, comment)
    }

    return B({ className: style.component }, content, this.renderToggle())
  }
})