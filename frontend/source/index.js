import { render } from 'react-dom'
import router from './application/router.js'
import store from './application/store.js'


render(
  store.connect(router),
  document.getElementById('application')
)