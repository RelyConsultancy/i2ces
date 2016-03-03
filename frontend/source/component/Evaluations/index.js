import { Component, B } from '/component/component.js'
import Title from '/component/PaneTitle'
import store from '/application/store.js'
import style from './style.css'


const Evaluations = Component({
  class: true,
  render () {
    const { store, children } = this.props

    const attrs = {
      className: style.component,
    }

    return B(
      attrs,
      Title({ text: 'Campaign Evaluation Index' })
    )
  }
})


export default store.sync('evaluations', Evaluations)