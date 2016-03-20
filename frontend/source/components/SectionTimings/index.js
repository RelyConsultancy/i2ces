import { Component, B, Link } from '/components/component.js'
import { fmtDate } from '/application/utils.js'
import store from '/application/store.js'
import style from './style.css'


const { stages } = store.getState().evaluation


export default Component({
  render () {
    const { component } = this.props

    const items = component.items.map((item, key) => B(
      { className: style.stage, key },
      B({ className: style.stage_label }, stages[item.label] + ':'),
      `${fmtDate(item.date_start)} - ${fmtDate(item.date_end)}`
    ))

    return B({ className: style.stages }, items)
  }
})