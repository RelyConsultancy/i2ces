import { Component, B, Image, Link } from '/components/component.js'
import Title from '/components/PaneTitle'
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