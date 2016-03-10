import { B } from '/component/component.js'
import { getUnique } from '/application/utils.js'
import Grid from '/component/Grid'
import Select from '/component/Select'
import { setFilter } from '/application/actions.js'
import style from './style.css'


const getBrand = (item) => (item.brand)
const getSupplier = (item) => (item.supplier.name)
const getCategory = (item) => (item.category)
const setOption = (value) => ({ value, label: value })


export default ({ store }) => {
  if (!store.list.length) return null

  const categories = Select({
    placeholder: 'Category',
    searchable: false,
    options: getUnique(store.list.map(getCategory)).map(setOption),
    value: store.filter.category,
    onChange: (option) => {
      setFilter('category', option ? option.value : null)
    },
  })

  const brands = Select({
    placeholder: 'Brand',
    searchable: false,
    options: getUnique(store.list.map(getBrand)).map(setOption),
    value: store.filter.brand,
    onChange: (option) => {
      setFilter('brand', option ? option.value : null)
    },
  })

  const suppliers = Select({
    placeholder: 'Supplier',
    searchable: false,
    options: getUnique(store.list.map(getSupplier)).map(setOption),
    value: store.filter.supplier,
    onChange: (option) => {
      setFilter('supplier', option ? option.value : null)
    },
  })

  const label = B({ className: style.filters_label }, 'Filter by')

  const filters = Grid({
    blocks: 3,
    items: [
      brands,
      suppliers,
      categories,
    ]
  })

  const attrs = {
    className: style.filters
  }
  return B(attrs, label, filters)
}
