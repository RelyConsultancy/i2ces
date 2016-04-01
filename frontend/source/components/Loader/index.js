import { Component, B } from '/components/component.js'
import style from './style.css'


export default Component({
  displayName: 'Loader',
  render () {
    const className = `${style.loader} ${this.props.className}`

    return B(
      { className },
      B({ className: style.item_1 }),
      B({ className: style.item_2 }),
      B({ className: style.item_3 }),
      B({ className: style.item_4 }),
      B({ className: style.item_5 })
    )
  }
})