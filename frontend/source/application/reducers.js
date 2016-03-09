import { combineReducers } from 'redux'
import assign from 'object-assign'
import defaults from './state.js'


const dashboard = (state = defaults.dashboard, { type, data }) => {
  state = assign({}, state)

  switch (type) {
    case 'dashboard.network':
      state.network = data
    break
  }

  return state
}


const evaluations = (state = defaults.evaluations, { type, data }) => {
  state = assign({}, state)

  switch (type) {
    case 'evaluations.list':
      state.list = data
    break

    case 'evaluations.filter':
      state.filter[data.filter] = data.value
    break

    case 'evaluation.document':
      state.document = data
    break
  }

  return state
}


export default combineReducers({
  dashboard,
  evaluations,
})