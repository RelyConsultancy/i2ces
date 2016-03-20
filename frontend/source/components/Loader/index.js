import { Component, B } from '/components/component.js'
import style from './style.css'


export default Component({
  displayName: 'Loader',
  render () {
    const className = `${style.loader} ${this.props.className}`

    return B(
      { className },
      B({ className: style.line1 }),
      B({ className: style.line2 }),
      B({ className: style.line3 }),
      B({ className: style.line4 }),
      B({ className: style.line5 })
    )
  }
})