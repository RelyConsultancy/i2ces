import { Component, B } from '/components/component.js'
import style from './style.css'
import { fmtDate, fmtUnit, getInitials } from '/application/utils.js'


const ObjectiveBlob = ({ label, value, unit }) => {
    console.log()
    value = B({ className: style.result_value }, fmtUnit(value, unit))
    return B({ className: 'i2c_objective_blob' }, B({ className: 'i2c_objective_blob_inner'}, B({ className: 'i2c_objective_title' }, label), B({ className: 'i2c_objective_value' }, value)));
    //return B({ className: style.result_label, key: index }, label, value)
    
}

export default Component({
  getInitialState () {
    return this.props
  },
  render () {
    const data = this.props
    console.log(data)
    if (data) {
        return B({ className: style.blob }, ObjectiveBlob(data))
    } else {
        return B('')
    }
  }
})