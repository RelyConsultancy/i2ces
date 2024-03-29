import { Component, B } from '/components/component.js'
import { fmtUnit } from '/application/utils.js'
import style from './style.css'
import ObjectiveBlob from '/components/ObjectiveBlob'
import _ from 'underscore'

const Objectives2 = ({ items }) => {
  const count = items.length
  const rows = []
  
  
  if (_.find(items, (item) => { 
      return item.label.toLowerCase() == 'acquire new customers' }) !== undefined 
        && _.find(items, (item) => { return item.label.toLowerCase() == 'launch new product' }) !== undefined) {
      items = _.reject(items, (item) => { return item.label.toLowerCase() == 'acquire new customers' })
  }
  
  console.log(items);
  
  items = items.map((data, index) => ObjectiveBlob(data))
  
  
  
  const splice = items.lenght % 2 == 0 ? 4 : 3

  while(items.length) {
    rows.push(B({className: 'i2c_objective_list_row_wrapper'}, B({className: 'i2c_objective_list_row' }, ...items.splice(0,splice))))
  }

  return B({ className: style.list }, B({ className: 'i2c_objectives_list' }, ...rows))
}

export default Component({
  render () {
    const { component } = this.props
    const header = B({ className: style.list_header }, 'Campaign Objectives')

    /*
    const items = component.items.map((item) => {
      const value = fmtUnit(item.value, item.unit)
      const listValue = B({ className: style.list_value }, value)

      return B({ className: style.list_item }, item.label, listValue)
    })
    */

    return B({ className: style.list }, header, Objectives2(component))
  }
})
