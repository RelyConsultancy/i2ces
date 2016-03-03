import { createElement } from 'react'
import { Router, browserHistory } from 'react-router'
import routes from './routes.js'


const router = createElement(Router, {
  history: browserHistory,
  routes,
})


export default router