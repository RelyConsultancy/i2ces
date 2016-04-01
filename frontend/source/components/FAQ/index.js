import { Component, B, Image, Link } from '/components/component.js'
import store from '/application/store.js'
import style from './style.css'


const FAQ = Component({
  class: true,
  render () {
    const { store, children, dispatch } = this.props
    const header = B({ className: style.header }, 'FAQs')

    return B({ className: style.faq }, header)
  }
})


export default store.sync('faq', FAQ)