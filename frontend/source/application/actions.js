import store from './store.js'
import http from './http.js'


const { dispatch } = store


export const setNetworkIndicator = (isVisible) => {
  dispatch({
    type: 'dashboard.network',
    data: isVisible,
  })
}


export const setFilter = (filter, value) => {
  dispatch({
    type: 'evaluations.filter',
    data: { filter, value },
  })
}


export const fetchEvaluations = () => {
  http('get', '/api/evaluations', (reply) => {
    dispatch({
      type: 'evaluations.list',
      data: reply.data.items,
    })
  })
}


export const fetchEvaluation = (id) => {
  http('get', `/api/evaluations/${id}`, (reply) => {
    dispatch({
      type: 'evaluation.document',
      data: reply.data,
    })
  })
}
