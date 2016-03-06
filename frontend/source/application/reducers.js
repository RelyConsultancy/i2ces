import { combineReducers } from 'redux'
import assign from 'object-assign'
import defaults from './state.js'


const dashboard = (state = defaults.dashboard, { type, data }) => {
  switch (type) {
    case 'dashboard.network':
      state = assign({}, state)
      state.network = data
    break
  }

  return state
}


const evaluations = (state = defaults.evaluations, { type, data }) => {
  switch (type) {
    case 'evaluations.list':
      state = assign({}, state)
      state.list = data
    break

    case 'evaluations.filter':
      state = assign({}, state)
      state.filter[data.filter] = data.value
    break
  }

  return state
}


export default combineReducers({
  dashboard,
  evaluations,
})