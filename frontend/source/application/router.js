import { createElement } from 'react'
import { Router, browserHistory, hashHistory } from 'react-router'
import routes from './routes.js'


const router = createElement(Router, {
  history: hashHistory,
  routes,
})


export default router