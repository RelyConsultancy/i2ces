import store from './store.js'
import http from './http.js'
import { getUnique } from './utils.js'


/*
    helpers
*/
// no operation, default for request handlers
const noop = function () {}

const cmd = (type, data) => {
  store.dispatch({ type, data })
}


export const isUser = (type) => {
  const { user } = store.getState().dashboard

  if (Array.isArray(type)) {
    return type.indexOf(user.type) != -1
  }
  else {
    return user.type == type
  }
}


export const setURL = (path) => (
  store.getState().dashboard.user.host + path
)


export const isI2C = () => (
  isUser('i2c_employee')
)


// check if evaluation is editable
export const isEditable = () => {
  const { user } = store.getState().dashboard
  const { evaluation } = store.getState().evaluation

  const isDraft = evaluation.state == 'draft'
  const isEditable = user.edit.indexOf(evaluation.cid) != -1

  return isDraft && isEditable
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


export const setChapter = (data) => {
  cmd('evaluation.chapters_cache', data)
}


export const setChapterSection = (data) => {
  cmd('evaluation.chapter_section', data)
}


export const setEvaluation = (data) => {
  cmd('evaluation.evaluation', data)
}


export const updateChapter = ({ cid, chapter }, handler = noop) => {
  const { id } = chapter
  const options = { data: chapter }

  http('post', `/api/evaluations/${cid}/chapters/${id}`, options, (data) => {
    handler(data)
  })
}


export const fetchChapter = ({ cid, id }, handler = noop) => {
  http('get', `/api/evaluations/${cid}/chapters/${id}`, (data) => {
    setChapter(data)
    handler(data)
  })
}


export const fetchDataset = (source, handler = noop) => {
  http('get', source, (data) => {
    handler(data)
  })
}


export const fetchEvaluations = (handler = noop) => {
  const { user } = store.getState().dashboard
  const options = {
    data: { cids: user.view }
  }

  http('post', '/api/evaluations', options, (data) => {
    cmd('evaluation.list', data.items)
    handler(data)
  })
}


export const fetchEvaluation = ({ cid }, handler = noop) => {
  http('get', `/api/evaluations/${cid}`, (data) => {
    setEvaluation(data)
    handler(data)
  })
}


export const mutateEvaluation = ({ cid, data }, handler = noop) => {
  if (data.state) {
    const { user } = store.getState().dashboard
    const { evaluation } = store.getState().evaluation
    const action = (data.state == 'draft' ? 'unpublish' : 'publish')

    // set evaluation state
    evaluation.state = (data.state == 'draft' ? 'draft' : 'published')

    // remove evaluation form editable list
    if (data.state == 'published') {
      user.edit.splice(user.edit.indexOf(evaluation.cid), 1)
    }
    else {
      user.edit.push(evaluation.cid)
    }

    http('post', `/api/evaluations/${cid}/${action}`, (data) => {
      handler(data)
    })
  }
}