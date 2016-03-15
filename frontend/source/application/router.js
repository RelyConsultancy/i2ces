import { createElement } from 'react'
import { Router, useRouterHistory } from 'react-router'
import { createHashHistory } from 'history'
import routes from './routes.js'


const history = useRouterHistory(createHashHistory)({ queryKey: false })
const router = createElement(Router, { history, routes })


export default router