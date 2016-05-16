import { Component, B } from '/components/component.js'
import style from './style.css'


export default ({ evaluation }) => {
  const title = B({ className: style.cover_title }, 'Thank you')
  const className = `${style.cover_outro} page_break_before`

  return B({ className }, title)
}