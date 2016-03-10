import store from './store.js'
import http from './http.js'
import { getUnique } from './utils.js'


const cmd = (type, data) => {
  store.dispatch({ type, data })
}


/*
    dashboard
*/
export const setUser = (data) => {
  cmd('dashboard.user', data)
}


export const setFlagNetwork = (isVisible) => {
  cmd('dashboard.flag.network', isVisible)
}


/*
    evaluation
*/
export const setFilter = (filter, value) => {
  cmd('evaluation.filter', { filter, value })
}

export const setEvaluation = (data) => {
  cmd('evaluation.evaluation', data)
}

export const setChapter = (data) => {
  cmd('evaluation.chapters_cache', data)
}

export const setChapterSection = (data) => {
  cmd('evaluation.chapter_section', data)
}

export const fetchEvaluations = () => {
  const { dashboard } = store.getState()
  const { view, edit } = dashboard.user
  const cids = getUnique([].concat(
    view.map(i => i.cid),
    edit.map(i => i.cid)
  ))
  const data = { cids }

  http('post', '/api/evaluations', { data }, (reply) => {
    cmd('evaluation.list', reply.data.items)
  })
}

export const fetchEvaluation = (id) => {
  http('get', `/api/evaluations/${id}`, (reply) => {
    setEvaluation(reply.data)
  })
}

export const fetchChapter = ({ cid, id }) => {
  http('get', `/api/evaluations/${cid}/chapters/${id}`, (reply) => {
    setChapter(reply.data)
  })
}