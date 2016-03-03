import { createElement } from 'react'
import { createStore, applyMiddleware, combineReducers } from 'redux'
import { Provider, connect } from 'react-redux'
import thunk from 'redux-thunk'
import dashboard from '/component/Dashboard/reducer.js'


const reducers = combineReducers({
  dashboard,
})

const thunkify = applyMiddleware(thunk)
const store = thunkify(createStore)(reducers)

store.connect = (component) => (
  createElement(Provider, { store }, component)
)

store.sync = (name, component) => (
  connect((store) => ({ store: store[name] }))(component)
)


export default store