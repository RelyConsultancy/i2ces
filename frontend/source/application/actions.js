import store from './store.js'
import { get, post } from './http.js'


const { dispatch } = store


export const setNetworkIndicator = (isVisible) => {
  dispatch({
    type: 'dashboard.network',
    data: isVisible,
  })
}


export const fetchEvaluations = () => {
  get('/api/evaluations', (reply) => {
    dispatch({
      type: 'evaluations.list',
      data: reply.data.items,
    })
  })
}


export const setFilter = (filter, value) => {
  dispatch({
    type: 'evaluations.filter',
    data: { filter, value },
  })
}