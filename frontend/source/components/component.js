import { createClass, createElement, PropTypes } from 'react'
import { Link as link } from 'react-router'
import { isElement, isString, isNumber} from '/application/utils.js'
import textarea from 'react-textarea-autosize'


export const Element = (type) => (
  (el, ...children) => {
    const args = (isElement(el) || isString(el) || isNumber(el))
               ? [type, null, el]
               : [type, el]

    return createElement.apply(null, args.concat(children))
  }
)


export const Component = (source) => {
  if (!source) {
    throw Error('Component() called with no arguments')
  }

  const component = createClass(source)

  if (source.class) {
    return component
  }

  return Element(component)
}


// DOM react elements
export const B = Element('b')
export const A = Element('a')
export const Button = Element('button')
export const Input = Element('input')
export const Textarea = Element(textarea)
export const Image = Element('img')
export const Link = Element(link)
export const SVG = Element('svg')
export const Table = Element('table')
export const TBody = Element('tbody')
export const TR = Element('tr')
export const TD = Element('td')
