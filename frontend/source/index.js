import { render } from 'react-dom'
import router from './application/router.js'
import store from './application/store.js'
import http from './application/http.js'
import { loadCSS } from './application/utils.js'
import { setUser } from './application/actions.js'

// load icon font (also user by Froala)
loadCSS('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css')


http('get', '/api/me', (reply) => {
  if (reply.error) {
    alert(`Server error: ${reply.error}`)
  }
  else {
    setUser(reply.data)

    render(
      store.connect(router),
      document.getElementById('application')
    )
  }
})