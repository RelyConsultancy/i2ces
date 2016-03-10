import 'whatwg-fetch' // window.fetch polyfill
import qs from 'qs'
import { isFunction } from './utils.js'
import { setFlagNetwork } from './actions.js'


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

const onError = (error) => {
  console.error(error.stack)
}

const fmtQuery = (data) => (
  '?' + qs.stringify(data)
)

const onReply = (reply) => {
  hideLoader()

  try {
    reply = reply.json()
  }
  catch (error) {
    throw error
  }

  return reply
}


const defaults = {
  // set to send cookies
  credentials: 'same-origin',
  headers: {
    // ORO header required
    'X-CSRF-Header': 1,
  },
}


export default (method, url, options, handler) => {
  // make `options` an optional argument
  if (isFunction(options)) {
    handler = options; options = {}
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

  showLoader()

  fetch(url, config)
    .then(onReply)
    .then(handler)
    .catch(onError)
}