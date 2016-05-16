import { Component, B } from '/components/component.js'
import style from './style.css'


export default Component({
  render () {
    const { component } = this.props

    const items = component.items.map((item, key) => (
      B({ className: style.list_item, key }, item)
    ))

    return B({ className: style.list }, items)
  }
})