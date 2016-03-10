import { Component, B, Link } from '/component/component.js'
import style from './style.css'


const ListSection = Component({
  getInitialState () {
    return { isEditable: false }
  },
  render () {
    const { component } = this.props
    const { isEditable } = this.state

    const list = component.items.map((item, key) => (
      B({ className: style.list_item, key }, item)
    ))


    return B({ className: style.list }, list)
  }
})


export default ListSection