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
      // reset other filters
      for (let name in state.filter) {
        state.filter[name] = null
      }
      state.filter[data.filter] = data.value
    break

    case 'evaluation.evaluation':
      state.evaluation = data
      // clear cache
      state.chapters_cache = {}
      state.chapter_section = null
    break

    case 'evaluation.chapters_cache':
      state.chapters_cache[data.id] = data
      state.chapter_section = data.content ? data.content[0] : null
    break

    case 'evaluation.chapter_section':
      state.chapter_section = data
    break
  }

  return state
}


export default combineReducers({
  dashboard,
  evaluation,
})