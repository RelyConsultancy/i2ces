import { Component, B, Image, Link } from '/component/component.js'
import Title from '/component/PaneTitle'
import store from '/application/store.js'
import style from './style.css'


const FAQ = Component({
  class: true,
  render () {
    const { store, children, dispatch } = this.props

    const attrs = {
      className: style.component,
    }

    return B(
      attrs,
      Title({ text: 'FAQs' })
    )
  }
})


export default store.sync('faq', FAQ)