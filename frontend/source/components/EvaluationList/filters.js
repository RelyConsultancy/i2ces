import { B } from '/components/component.js'
import Grid from '/components/Grid'
import Select from '/components/Select'
import { getUnique } from '/application/utils.js'
import { setFilter, isUser } from '/application/actions.js'
import style from './style.css'


const fmtOptions = (items) => (
  items.map((item) => ({ value: item, label: item }))
)


const Categories = ({ store }) => {
  const { brand, supplier } = store.filter
  const items = store.list.filter((item) => {
    return true
    if (brand && brand != item.brand) return false
    if (supplier && supplier != item.supplier.name) return false
  })

  const options = {
    placeholder: 'Category',
    value: store.filter.category,
    options: fmtOptions(getUnique(items.map(i => i.category))),
    searchable: false,
    onChange: (option) => {
      setFilter('category', option ? option.value : null)
    },
  }

  return Select(options)
}


const Brands = ({ store }) => {
  const { category, supplier } = store.filter
  const items = store.list.filter((item) => {
    return true
    if (category && category != item.category) return false
    if (supplier && supplier != item.supplier.name) return false
  })

  const options = {
    placeholder: 'Brand',
    value: store.filter.brand,
    options: fmtOptions(getUnique(items.map(i => i.brand))),
    searchable: false,
    onChange: (option) => {
      setFilter('brand', option ? option.value : null)
    },
  }

  return Select(options)
}


const Suppliers = ({ store }) => {
  const { category, brand } = store.filter
  const items = store.list.filter((item) => {
    return true
    if (category && category != item.category) return false
    if (brand && brand != item.brand) return false
  })


  const options = {
    placeholder: 'Supplier',
    value: store.filter.supplier,
    options: fmtOptions(getUnique(items.map(i => i.supplier.name))),
    searchable: false,
    onChange: (option) => {
      setFilter('supplier', option ? option.value : null)
    },
  }

  return Select(options)
}


export default ({ store }) => {
  if (!store.list.length) return null

  const label = B({ className: style.filters_label }, 'Filter by')
  const items = [
    Brands({ store }),
    isUser('i2c_employee') ? Suppliers({ store }) : null,
    Categories({ store }),
  ]

  const filters = Grid({
    blocks: 3,
    items: items.filter(i => i)
  })

  return B({ className: style.filters }, label, filters)
}