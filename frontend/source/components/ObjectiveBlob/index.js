import { Component, B } from '/components/component.js'
import style from './style.css'
import { fmtDate, fmtUnit, getInitials } from '/application/utils.js'


const Blob = ({ label, value, unit }) => {
    
    value = B({ className: style.result_value }, fmtUnit(value, unit))
    return B({ className: style.i2c_objective_blob }, B({ className: 'i2c_objective_blob_inner'}, B({ className: 'i2c_objective_title' }, label), B({ className: 'i2c_objective_value' }, fmtUnit(value, unit))));
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