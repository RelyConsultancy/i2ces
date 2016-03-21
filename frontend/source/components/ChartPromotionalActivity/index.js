import d3 from 'd3'
import { Component, B, Table, TBody, TR, TD } from '/components/component.js'
import Froala from '/components/Froala'
import Chart from '/components/Chart'
import store from '/application/store.js'
import { fetchDataset } from '/application/actions.js'
import { fmtUnit, fmtHTML, fmtDate } from '/application/utils.js'
import style from './style.css'


const byOffer = (i => i.type.toLowerCase() == 'offer')
const byBrand = (i => i.type.toLowerCase() == 'brand')
const byCompetitor = (i => i.type.toLowerCase() == 'competitor')
const byDate = (a, b) => (a.date < b.date ? -1 : 1)
const fmtChart = (i => i.value)

const setRegion = (stage) => {
  const region = {
    axis: 'x',
    class: `stage_${stage.label}`,
  }

  if (stage.label != 'pre') {
    region.start =  stage.date_start
  }

  if (stage.label != 'post') {
    region.end =  stage.date_end
  }

  return region
}


const ActivityChart = ({ data, timings }) => {
  const dates = data.filter(byOffer).sort(byDate).map(i => i.date)
  const offer = data.filter(byOffer).sort(byDate).map(fmtChart)
  const brand = data.filter(byBrand).sort(byDate).map(fmtChart)
  const competitor = data.filter(byCompetitor).sort(byDate).map(fmtChart)

  const chart = Chart({
    type: 'bar',
    data: {
      type: 'bar',
      x: 'Dates',
      columns: [
        ['Dates'].concat(dates),
        ['Offer'].concat(offer),
        ['Brand'].concat(brand),
        ['Competitor'].concat(competitor),
      ],
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
        tick: { format: d3.format('1%') },
        padding: { top: 0, bottom: 0 },
      },
    },
    tooltip: { show: false },
    padding: { top: 25 },
    bar: {
      width: { ratio: 0.6 }
    },
    regions: timings.map(setRegion)
  })


  const label = B(
    { className: style.chart_label },
    '% Products on promotion'
  )

  return B({ className: style.chart }, label, chart)
}


const { stages } = store.getState().evaluation

const Stages = ({ timings }) => (
  B({ className: style.stages }, timings.map((item, key) => {
    const period = fmtDate(item.date_start) +' - '+ fmtDate(item.date_end)
    const date = B({ className: style.stage_period }, period)
    const label = stages[item.label] + ': '

    return B({ className: style.stage, key }, label, date)
  }))
)


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
    const { timings } = component

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

      const chart = ActivityChart({ data, timings })
        const stages = Stages({ timings })

      content = B(info, chart, stages, comment)
    }

    return B({ className: style.component }, content, this.renderToggle())
  }
})