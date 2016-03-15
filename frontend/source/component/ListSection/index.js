import { Component, B, Link } from '/component/component.js'
import style from './style.css'


const ListSection = Component({
  render () {
    const { component } = this.props

    const list = component.items.map((item, key) => (
      B({ className: style.list_item, key }, item)
    ))

    return B({ className: style.list }, list)
  }
})


export default ListSection