import moment from 'moment'
import { Component, B, Link } from '/component/component.js'
import Title from '/component/PaneTitle'
import Select from '/component/Select'
import store from '/application/store.js'
import { fetchEvaluations, setFilter } from  '/application/actions.js'
import style from './style.css'


const fmtDate = (date) => (
  moment(date, 'YYYY-MM-DD').format('DD.MM.YYYY')
)

const Grid3 = (...items) => (
  B({ className: 'grid-blocks-3' }, items.map(
    (item, key) => B({ key }, item)
  ))
)

const getBrand = (item) => (item.brand)
const getSupplier = (item) => (item.supplier.name)
const getCategory = (item) => (item.category)
const getUnique = (value, index, array) => (
  array.indexOf(value) === index
)
const setOption = (value) => ({ value, label: value })


const Filters = ({ store }) => {
  if (!store.list.length) return null

  const categories = Select({
    placeholder: 'Category',
    searchable: false,
    options: store.list.map(getCategory).filter(getUnique).map(setOption),
    value: store.filter.category,
    onChange: (option) => {
      setFilter('category', option ? option.value : null)
    },
  })

  const brands = Select({
    placeholder: 'Brand',
    searchable: false,
    options: store.list.map(getBrand).filter(getUnique).map(setOption),
    value: store.filter.brand,
    onChange: (option) => {
      setFilter('brand', option ? option.value : null)
    },
  })

  const suppliers = Select({
    placeholder: 'Supplier',
    searchable: false,
    options: store.list.map(getSupplier).filter(getUnique).map(setOption),
    value: store.filter.supplier,
    onChange: (option) => {
      setFilter('supplier', option ? option.value : null)
    },
  })

  const label = B({ className: style.filters_label }, 'Filter by')

  const filters = Grid3(
    brands,
    suppliers,
    categories
  )

  const attrs = {
    className: style.filters
  }
  return B(attrs, label, filters)
}


const Item = (item, index) => {
  const title = B(
    { className: style.item_title },
    `${item.brand}: ${item.title}`
  )

  const date = B(
    { className: style.item_date },
    `${fmtDate(item.start_date)} - ${fmtDate(item.end_date)}`
  )

  const view = Link({
    to: `/evaluations/${item.id}`,
    className: style.item_view,
  }, 'View')

  const attrs = {
    key: index,
    className: style.item,
  }
  return B(attrs, title, date, view)
}


const List = ({ store }) => {
  const { category, brand, supplier } = store.filter
  const setFilters = (item) => {
    if (category && category != item.category) return false
    if (brand && brand != item.brand) return false
    if (supplier && supplier != item.supplier.name) return false

    return true
  }

  let content = store.list.filter(setFilters).map(Item)

  if (!content.length) {
    content = B({ className: style.list_empty }, store.list_empty)
  }

  return B({ className: style.list }, content)
}


const Evaluations = Component({
  class: true,
  componentDidMount () {
    fetchEvaluations()
  },
  render () {
    const { store, children } = this.props

    return B(
      Title({ text: 'Campaign Evaluation Index' }),
      Filters({ store }),
      List({ store })
    )
  }
})


export default store.sync('evaluations', Evaluations)