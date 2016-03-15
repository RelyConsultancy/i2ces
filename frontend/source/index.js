import { render } from 'react-dom'
import router from './application/router.js'
import store from './application/store.js'
import http from './application/http.js'
import { setUser } from './application/actions.js'


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