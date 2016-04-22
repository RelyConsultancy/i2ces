import 'whatwg-fetch' // window.fetch polyfill
import assign from 'object-assign'
import qs from 'qs'
import { isFunction } from './utils.js'
import { setFlagNetwork } from './actions.js'
import { saveAs } from './http_download.js'


let inFlight = 0

const showLoader = () => {
  if (!inFlight) {
    setFlagNetwork(true)
  }

  inFlight += 1
}

const hideLoader = () => {
  inFlight -= 1

  if (!inFlight) {
    // allow a timeout to visually notice the indicator
    setTimeout(setFlagNetwork, 300, false)
  }
}

const toJSON = (reply) => {
  try {
    return reply.json()
  }
  catch (error) {
    console.warn('HTTP: error while parsing JSON')
    throw error
  }
}

const fmtQuery = (data) => (
  '?' + qs.stringify(data)
)

const defaults = {
  // set to send cookies
  credentials: 'same-origin',
  headers: {
    // ORO header required
    'X-CSRF-Header': 1,
  },
}


export const download = (url, filename) => {
  const config = assign({ method: 'GET' }, defaults)

  fetch(url, config)
    .then(reply => reply.blob())
    .then((blob) => {
      saveAs(blob, filename)
    })
}


export default (method, url, options, handler) => {
  // make `options` an optional argument
  if (isFunction(options)) {
    handler = options; options = {}
  }

  const config = assign({}, defaults, { method })

  if (options.data) {
    config.body = JSON.stringify(options.data)
    config.headers['Accept'] = 'application/json'
    config.headers['Content-Type'] = 'application/json'
  }

  if (options.query) {
    url += fmtQuery(options.query)
  }

  showLoader()

  fetch(url, config)
    .then(toJSON)
    .then((reply) => {
      hideLoader()

      if (reply.error) {
        console.warn('HTTP: response error')
        console.error(reply.error)
      }
      else if (handler) {
        handler(reply)
      }
    })
    .catch((error) => {
      console.warn(`HTTP: ${error.toString()}`)
      console.error(error.stack)
    })
}