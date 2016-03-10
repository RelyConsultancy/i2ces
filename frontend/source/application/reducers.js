import { combineReducers } from 'redux'
import assign from 'object-assign'
import defaults from './state.js'


const dashboard = (state = defaults.dashboard, { type, data }) => {
  state = assign({}, state)

  switch (type) {
    case 'dashboard.user':
      state.user = data
    break

    case 'dashboard.flag.network':
      state.flag.network = data
    break
  }

  return state
}


const evaluation = (state = defaults.evaluation, { type, data }) => {
  state = assign({}, state)

  switch (type) {
    case 'evaluation.list':
      state.list = data
    break

    case 'evaluation.filter':
      state.filter[data.filter] = data.value
    break

    case 'evaluation.data':
      state.data = data
    break

    case 'evaluation.chapter':
      state.chapter[data.id] = data
    break
  }

  return state
}


export default combineReducers({
  dashboard,
  evaluation,
})