import { Component, B } from '/component/component.js'
import style from './style.css'


const Title = Component({
  render () {
    const { text } = this.props

    return B(
      { className: style.component },
      B({ className: style.wrap }, text)
    )
  }
})


export default Title