import { Component, B } from '/component/component.js'
import style from './style.css'


const Loader = Component({
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


export default Loader