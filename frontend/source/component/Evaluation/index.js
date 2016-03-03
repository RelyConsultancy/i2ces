import { Component, B } from '/component/component.js'
import store from '/application/store.js'
import style from './style.css'


const Evaluation = Component({
  class: true,
  render () {
    const { store, children } = this.props

    const attrs = {
      className: style.component,
    }

    return B(
      attrs,
      'evaluation'
    )
  }
})


export default store.sync('evaluation', Evaluation)