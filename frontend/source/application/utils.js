import moment from 'moment'
import { B } from '/components/component.js'


export const isElement = (value) => (
  typeof value === 'object' && value !== null && value.$$typeof
)

export const isFunction = (value) => (
  typeof value == 'function'
)

export const isString = (value) => (
  typeof value == 'string'
)

export const isNumber = (value) => (
  typeof value == 'number'
)

export const getUnique = (array) => (
  array.filter((value, index, array) => (
    array.indexOf(value) === index
  ))
)


export const getInitials = (string) => (
  string.split(/\s+/).map(s => s.charAt(0).toUpperCase()).join('')
)


export const slugify = (string) => (
  string.toLowerCase().trim()
    // replace & with 'and'
    .replace(/&/g, '-and-')
    // replace spaces, non-word characters and dashes with a single dash (-)
    .replace(/[\s\W-]+/g, '-')
)


// react HTML insert
export const fmtHTML = (string) => (
  B({ dangerouslySetInnerHTML: { __html: string } })
)


export const fmtDate = (date) => (
  moment(date, 'YYYY-MM-DD').format('DD MMM YYYY')
)


export const fmtCurrency = (value, sign = '£') => (
  sign + parseInt(value).toLocaleString()
)


export const fmtNumber = (value) => (
  parseInt(value).toLocaleString()
)


export const fmtUnit = (value, unit = "") => {
  switch (unit.toLowerCase()) {
    case 'money':
    case 'currency':
    case 'gbp':
      value = fmtCurrency(value)
    break

    case 'ppts':
      value += 'ppts'
    break

    case 'percent':
      value = parseFloat(value).toFixed(2) + '%'
    break

    default:
      value = parseInt(value)
  }

  return value
}


export const loadCSS = (path) => {
  const link = document.createElement('link')

  link.rel = 'stylesheet'
  link.type = 'text/css'
  link.href = path

  document.head.appendChild(link)
}