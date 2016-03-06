import { createElement } from 'react'
import { createStore, applyMiddleware } from 'redux'
import { Provider, connect } from 'react-redux'
import thunk from 'redux-thunk'
import reducers from './reducers.js'


const middleware = applyMiddleware(thunk)
const setStore = middleware(createStore)
const store = setStore(reducers)

store.connect = (component) => (
  createElement(Provider, { store }, component)
)

store.sync = (name, component) => (
  connect((store) => ({ store: store[name] }))(component)
)


export default store