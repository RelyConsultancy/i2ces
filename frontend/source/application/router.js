import { createElement } from 'react'
import { Router, hashHistory } from 'react-router'
import routes from './routes.js'


export default createElement(Router, { routes, history: hashHistory })