import { render } from 'react-dom'
import router from './router.js'
import store from './store.js'
import http from './http.js'
import { loadCSS } from './utils.js'
import { setUser } from './actions.js'


// load icon font (also user by Froala)
loadCSS('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css')


http('get', '/api/me', (user) => {
  setUser(user)

  render(
    store.connect(router),
    document.getElementById('application')
  )
})