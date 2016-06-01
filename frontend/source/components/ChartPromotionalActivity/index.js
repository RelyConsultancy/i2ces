import d3 from 'd3'
import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import store from '/application/store.js'
import { fetchDataset } from '/application/actions.js'
import { fmtUnit, fmtDate } from '/application/utils.js'
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


const ActivityChart = ({ data, timings, isPDF }) => {
  const dates = data.filter(byOffer).sort(byDate).map(i => i.date)
  const offer = data.filter(byOffer).sort(byDate).map(fmtChart)
  const brand = data.filter(byBrand).sort(byDate).map(fmtChart)
  const competitor = data.filter(byCompetitor).sort(byDate).map(fmtChart)

  const chart = Chart({
    className: isPDF && style.chart_pdf,
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
      colors: {
        'Brand': '#ed7b29',
        'Offer': '#4f81bd',
        'Competitor': '#9bbb59',
      }
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
    if (isPDF) {
        return B({ className: style.chartPDF }, label, chart)
    }
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
    const { component, isPDF } = this.props
    const { isEditable, data } = this.state
    const { timings } = component

    if (data.length) {
      const chart = ActivityChart({ data, timings, isPDF })
      const stages = Stages({ timings })

      return B(chart, stages)
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})