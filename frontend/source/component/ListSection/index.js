import { Component, B, Link } from '/component/component.js'
import style from './style.css'


const ListSection = Component({
  getInitialState () {
    return { isEditable: false }
  },
  render () {
    const { component } = this.props
    const { isEditable } = this.state
    const html = component.value

    return B(component.type)
  }
})


export default ListSection