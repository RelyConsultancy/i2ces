import { Component, B, Link } from '/components/component.js'
import Grid from '/components/Grid'
import { fmtDate, fmtUnit, getInitials } from '/application/utils.js'
import { fetchEvaluation } from '/application/actions.js'
import store from '/application/store.js'
import style from './style.css'


const Header = (text) => (
  B({ className: style.header, content: text })
)


const Content = (...data) => B(
  { className: style.content },
  B({ className: style.content_wrap }, ...data)
)


const Links = ({ store, params }) => {
  const back = Link({
    to: `/evaluations`,
    className: style.link,
  }, 'Back to Evaluation Index')

  return B({ className: style.links }, back)
}


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
  const title = B({ className: style.list_title}, 'Campaign Highlights')

  items = items.map(({ label, value, unit }, index) => {
    value = B({ className: style.result_value }, fmtUnit(value, unit))

    return B({ className: style.result_label, key: index }, label, value)
  })

  return B({ className: style.list }, title, items)
}


const Evaluation = Component({
  class: true,
  load () {
    const { store, params } = this.props

    if (!store.evaluation) {
      fetchEvaluation({ cid: params.cid })
    }
  },
  componentDidMount () {
    this.load()
  },
  render () {
    const { store, params } = this.props
    const { evaluation, evaluation_empty, chapter_palette } = store

    let content = B({ className: style.no_evaluation }, evaluation_empty)

    if (evaluation) {
      content = B(
        Links({ store, params }),
        Header(evaluation.display_title),
        Content(
          Date({ evaluation }),
          Channels({ items: evaluation.channels }),
          Chapters({ evaluation, colors: chapter_palette }),
          Objectives({ items: evaluation.campaign_objectives })
        )
      )
    }

    return content
  }
})


export default store.sync('evaluation', Evaluation)