import moment from 'moment'

export const isElement = (value) => (
  typeof value === 'object' && value !== null && value.$$typeof
)

export const isFunction = (value) => (
  typeof value == 'function'
)

export const isString = (value) => (
  typeof value == 'string'
)

export const getUnique = (array) => (
  array.filter((value, index, array) => (
    array.indexOf(value) === index
  ))
)

export const fmtDate = (date) => (
  moment(date, 'YYYY-MM-DD').format('DD MMM YYYY')
)

export const getInitials = (string) => (
  string.split(/\s+/).map(s => s.charAt(0).toUpperCase()).join('')
)

export const slugify = (string) => (
  string.toLowerCase().trim()
    .replace(/&/g, '-and-')      // Replace & with 'and'
    .replace(/[\s\W-]+/g, '-')   // Replace spaces, non-word characters and dashes with a single dash (-)
)

export const fmtUnit = (value, unit) => {
  switch (unit) {
    case 'GBP':
      value = '£' + value
    break

    case 'ppts':
      value = value + 'ppts'
    break
  }

  return value
}