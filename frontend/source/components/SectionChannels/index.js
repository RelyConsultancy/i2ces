import { Component, B } from '/components/component.js'
import style from './style.css'


export default ({ component }) => {
  const items = component.items.map((item) => B(
    { className: style.list_item, },
    B({ className: `icon_channel_${item.type}` }),
    item.label
  ))

  return B({ className: style.list}, ...items)
}
