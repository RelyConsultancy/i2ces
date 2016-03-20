import { Component, B, Image, Link } from '/components/component.js'
import store from '/application/store.js'
import style from './style.css'


const Header = (text) => (
  B({ className: style.header, content: text })
)


const FAQ = Component({
  class: true,
  render () {
    const { store, children, dispatch } = this.props

    const attrs = {
      className: style.component,
    }

    return B(
      attrs,
      Header('FAQs')
    )
  }
})


export default store.sync('faq', FAQ)