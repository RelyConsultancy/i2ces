import { Component, B, Link } from '/components/component.js'
import store from '/application/store.js'
import { fetchEvaluations, setEvaluation } from '/application/actions.js'
import { fmtDate } from '/application/utils.js'
import style from './style.css'
import Filters from './filters.js'


const Header = (text) => (
  B({ className: style.header, content: text })
)


const applyFilters = (store) => {
  const { category, brand, supplier } = store.filter
  const isVisible = (item) => {
    if (category && category != item.category) return false
    if (brand && brand != item.brand) return false
    if (supplier && supplier != item.supplier.name) return false

    return true
  }

  return store.list.filter(isVisible)
}


const Item = ({ data }) => {
  const state = data.state == 'published' ? null : B({
    className: style.item_state,
  }, data.state)

  const title = B(
    { className: style.item_title },
    data.display_title,
    state
  )

  const date = B(
    { className: style.item_date },
    `${fmtDate(data.start_date)} - ${fmtDate(data.end_date)}`
  )

  const view = Link({
    to: `/evaluations/${data.cid}`,
    className: style.item_view,
    onClick () {
      setEvaluation(data)
    }
  }, 'View')

  const attrs = {
    key: data.cid,
    className: style.item,
  }
  return B(attrs, title, date, view)
}


const List = ({ store }) => {
  let content = B({ className: style.list_empty }, store.list_empty)

  if (store.list.length) {
    content = applyFilters(store).map(data => Item({ data }))
  }

  return B({ className: style.list }, content)
}


const Evaluations = Component({
  class: true,
  componentDidMount () {
    fetchEvaluations()
  },
  render () {
    const { store } = this.props

    return B(
      Header('Campaign Evaluation Index'),
      B(
        { className: style.content },
        Filters({ store }),
        List({ store })
      )
    )
  }
})


export default store.sync('evaluation', Evaluations)