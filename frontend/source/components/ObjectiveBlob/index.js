import { Component, B } from '/components/component.js'
import style from './style.css'


const Blob = ({ label, value, unit }) => {
    
    value = B({ className: style.result_value }, fmtUnit(value, unit))
    return B({ className: style.result_label, key: index }, label, value)
    
}

export default Component({
  getInitialState () {
    return { on: this.props.on || false }
  },
  render () {
    const { data } = this.props
    
    return B({ className: style.blob }, Blob(data))
  }
})