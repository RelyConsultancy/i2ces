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
  try { reply = reply.json() }
  catch (error) {
    console.error(error, reply)
  }

  hideNetworkIndicator()

  return reply
}



const get = (url, options, callback) => {
  if (isFunction(options)) {
    callback = options
    options = {}
  }

  const config = {
    method: 'GET',
    // set to send cookies
    credentials: 'same-origin',
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


const post = (url, options, callback) => {
  if (isFunction(options)) {
    callback = options
    options = {}
  }

  const config = {
    method: 'POST',
    // set to send cookies
    credentials: 'same-origin',
  }

  if (options.data) {
    config.body = JSON.stringify(options.data)

    config.headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    }
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


export {
  get,
  post,
}