import { Component, B, Link } from '/component/component.js'
import Title from '/component/PaneTitle'
import Grid from '/component/Grid'
import { fetchEvaluation } from '/application/actions.js'
import { fmtDate, fmtUnit, getInitials } from '/application/utils.js'
import store from '/application/store.js'
import style from './style.css'


const Date = ({ evaluation }) => B(
  { className: style.date },
  B({ className: style.date_value }, `Start: ${fmtDate(evaluation.start_date)}`),
  B({ className: style.date_value }, `End: ${fmtDate(evaluation.end_date)}`)
)


const Channels = ({ items }) => {
  const title = B({ className: style.list_title}, 'Channels')

  items = items.map((item) => (
    B({ className: style.list_item }, item)
  ))

  items = Grid({ blocks: 2, items })

  return B({ className: style.list}, title, items)
}


const byOrder = (a, b) => (
  a.order > b.order ? 1 : -1
)


const Chapters = ({ evaluation, colors }) => (
  B({ className: style.chapters }, Grid({
    blocks: 2,
    items: evaluation.chapters.sort(byOrder).map((chapter) => {
      const color = colors[chapter.order - 1]
      const title = B({ className: style.chapter_title }, chapter.title)

      const arrow = B({
        className: style.chapter_arrow,
        style: { borderLeftColor: color },
      })

      const initials = B({
        className: style.chapter_initials,
        style: { backgroundColor: color },
      }, getInitials(chapter.title))

      const attrs = {
        to: `/evaluations/${evaluation.cid}/chapters/${chapter.id}`,
        className: style.chapter,
        style: { color },
      }
      return Link(attrs, initials, title, arrow)
    })
  }))
)


const Objectives = ({ items }) => {
  const title = B({ className: style.list_title}, 'Campaign Objectives')

  items = items.map(({ label, value, unit }, index) => {
    value = B({ className: style.objective_value }, fmtUnit(value, unit))

    return B({ className: style.list_item, key: index }, label, value)
  })

  return B({ className: style.list }, title, items)
}


const Evaluation = Component({
  class: true,
  load () {
    const { store, params } = this.props

    if (!store.evaluation) {
      fetchEvaluation(params.cid)
    }
  },
  componentDidMount () {
    this.load()
  },
  render () {
    const { store } = this.props
    const { evaluation, evaluation_empty, chapter_palette } = store

    let content = B({ className: style.no_evaluation }, evaluation_empty)

    if (evaluation) {
      content = B(
        Title({ text: evaluation.display_title }),
        B({ className: style.content }, B(
          { className: style.content_wrap },
          Date({ evaluation }),
          Channels({ items: evaluation.channels }),
          Chapters({ evaluation: evaluation, colors: chapter_palette }),
          Objectives({ items: evaluation.campaign_objectives })
        ))
      )
    }

    return content
  }
})


export default store.sync('evaluation', Evaluation)