import { Component, B, Link } from '/component/component.js'
import Title from '/component/PaneTitle'
import Grid from '/component/Grid'
import { fetchEvaluation } from '/application/actions.js'
import { fmtDate, fmtUnit, getInitials } from '/application/utils.js'
import store from '/application/store.js'
import style from './style.css'


const Date = ({ data }) => B(
  { className: style.date },
  B({ className: style.date_value }, `Start: ${fmtDate(data.start_date)}`),
  B({ className: style.date_value }, `End: ${fmtDate(data.end_date)}`)
)


const Channels = ({ items }) => {
  const title = B({ className: style.list_title}, 'Channels')

  items = items.map((item) => (
    B({ className: style.list_item }, item)
  ))

  items = Grid({ blocks: 2, items })

  return B({ className: style.list}, title, items)
}


const Chapters = ({ evaluation, colors }) => (
  B({ className: style.chapters }, Grid({
    blocks: 2,
    items: evaluation.chapters.map((chapter) => {
      const color = colors[chapter.id - 1]
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
  componentDidMount () {
    const { store, params } = this.props

    if (!store.data) {
      fetchEvaluation(params.id)
    }
  },
  render () {
    const { store } = this.props
    const { data, data_empty, chapter_palette } = store

    let content = B({ className: style.no_data }, data_empty)

    if (data) {
      content = B(
        Title({ text: data.display_title }),
        B({ className: style.content }, B(
          { className: style.info },
          Date({ data }),
          Channels({ items: data.channels }),
          Chapters({ evaluation: data, colors: chapter_palette }),
          Objectives({ items: data.campaign_objectives })
        ))
      )
    }

    return content
  }
})


export default store.sync('evaluation', Evaluation)