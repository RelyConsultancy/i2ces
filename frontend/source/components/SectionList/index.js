import { Component, B, Link } from '/components/component.js'
import style from './style.css'


const SectionList = Component({
  render () {
    const { component } = this.props

    const list = component.items.map((item, key) => (
      B({ className: style.list_item, key }, item)
    ))

    return B({ className: style.list }, list)
  }
})


export default SectionList