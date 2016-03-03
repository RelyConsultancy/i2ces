import Koa from 'koa'
import send from 'koa-send'


const koa = Koa()

koa.use(function * () {
  const options = {
    root: __dirname + '/public'
  }

  const isSent = yield send(this, this.path, options)

  if (!isSent) {
    yield send(this, 'index.html', options)
  }
})

koa.listen(3000)
