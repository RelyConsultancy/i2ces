import 'whatwg-fetch' // window.fetch polyfill
import qs from 'qs'
import { isFunction } from './utils.js'
import { setNetworkIndicator } from './actions.js'


let inFlight = 0

const showNetworkIndicator = () => {
  if (!inFlight) {
    setNetworkIndicator(true)
  }

  inFlight += 1
}

const hideNetworkIndicator = () => {
  inFlight -= 1

  if (!inFlight) {
    // allow a timeout to visually notice the indicator
    setTimeout(setNetworkIndicator, 300, false)
  }
}

const onError = (error) => {
  console.error(error)
}

const fmtQuery = (data) => (
  '?' + qs.stringify(data)
)

const fmtJSON = (reply) => {
  hideNetworkIndicator()

  return reply.json()
}


const defaults = {
  // set to send cookies
  credentials: 'same-origin',
  headers: {
    // ORO header required
    'X-CSRF-Header': 1,
  },
}


export default (method, url, options, callback) => {
  // make `options` an optional argument
  if (isFunction(options)) {
    callback = options; options = {}
  }

  const config = Object.assign({}, defaults, { method })

  if (options.data) {
    config.body = JSON.stringify(options.data)
    config.headers['Accept'] = 'application/json'
    config.headers['Content-Type'] = 'application/json'
  }

  if (options.query) {
    url += fmtQuery(options.query)
  }

  showNetworkIndicator()

  fetch(url, config)
    .then(fmtJSON)
    .then(callback)
    .catch(onError)
}