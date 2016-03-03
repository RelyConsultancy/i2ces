import assign from 'object-assign'
import defaults from './state.js'


export default (state = defaults, { type, data }) => {
  switch (type) {
    case 'dashboard.network':
      state = assign({}, state)
      state.network = data
    break
  }

  return state
}