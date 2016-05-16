import moment from 'moment'
import _ from 'underscore'
import qs from 'qs'
import { B } from '/components/component.js'
import numeral from 'numeral'

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

export const forEach = (list, handler) => {
  for (let i = 0; i < list.length; ++i) {
    handler(list[i], i)
  }
}


export const getInitials = (string) => (
  string.split(/\s+/).map(s => s.charAt(0).toUpperCase()).join('')
)


export const capitalize = (string) => (
  string[0].toUpperCase() + string.slice(1)
)


export const slugify = (string) => (
  string.toLowerCase().trim()
    // replace & with 'and'
    .replace(/&/g, '-and-')
    // replace spaces, non-word characters and dashes with a single dash (-)
    .replace(/[\s\W-]+/g, '-')
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

export const sortAlpha = (collection, key) => {
    return _.sortBy(collection, key);
}


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
    case 'percentage':
      value = parseFloat(value).toFixed(2) + '%'
    break

    default:
      value = numeral(parseInt(value)).format('0,0')
  }

  return value
}


export const getURLQuery = (hash) => {
  const query = location.hash.split('?')[1]

  return query ? qs.parse(query) : {}
}


export const loadCSS = (path) => {
  const link = document.createElement('link')

  link.rel = 'stylesheet'
  link.type = 'text/css'
  link.href = path

  document.head.appendChild(link)
}


/*
  options: {
    multiple - disallow multiple file select
  }
  onSelect - function called after the files have been selected
*/
export const filePicker = (options, onSelect) => {
  // make `options` optional
  if (!onSelect) onSelect = options

  const input = document.createElement('input')

  input.type = 'file'
  input.multiple = (options.multiple ? true : false)

  input.addEventListener('change', () => {
    onSelect(input.files)
  })

  input.click()
}
