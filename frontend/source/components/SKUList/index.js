import { Component, B } from '/components/component.js'
import _ from 'underscore'
import style from './style.css'


export default Component({
  render () {
    const { component } = this.props
    
    const items1 = component.items.slice(0, parseInt(component.items.length / 2)).map((item, key) => (
      B({ className: style.list_item, key }, item)
    ))
    
    const items2 = component.items.slice(parseInt(component.items.length / 2), component.items.length).map((item, key) => (
      B({ className: style.list_item, key }, item)
    ))
    
    console.log(items1);
    console.log(items2);
    
    return B({ className: style.list_columns }, B({ className: style.list }, items1), B({ className: style.list }, items2))
  }
})