import { Component, B } from '/components/component.js'
import { fmtUnit } from '/application/utils.js'
import style from './style.css'


export default ({ component }) => {
  const header = B({ className: style.list_header }, 'Campaign Objectives')

  const items = component.items.map((item) => {
    const value = fmtUnit(item.value, item.unit)
    const listValue = B({ className: style.list_value }, value)

    return B({ className: style.list_item }, item.label, listValue)
  })

  return B({ className: style.list }, header, ...items)
}