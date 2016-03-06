import Koa from 'koa'
import send from 'koa-send'
import apiMe from './samples/me.json'
import apiEvaluations from './samples/evaluations.json'


const root = __dirname + '/public'
const koa = Koa()

koa.use(function * () {
  const { path, headers } = this

  switch (path) {
    case '/api/me':
      this.body = apiMe
    break

    case '/api/evaluations':
      this.body = apiEvaluations
    break

    default:
      const isSent = yield send(this, path, { root })

      if (!isSent) {
        yield send(this, 'index.html', { root })
      }
  }
})

koa.listen(3000)
