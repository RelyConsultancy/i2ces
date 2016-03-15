import Koa from 'koa'
import send from 'koa-send'
import route from 'koa-route'
import bodyparser from 'koa-bodyparser'

import me from './samples/me.json'
import i2c1507187a from './samples/evaluation.i2c1507187a.json'
import i2c1509134a from './samples/evaluation.i2c1509134a.json'
import i2c1510047a from './samples/evaluation.i2c1510047a.json'
import i2c1510047a_chapters from './samples/chapters.i2c1510047a.json'


const db = {
  evaluations: {
    i2c1507187a,
    i2c1509134a,
    i2c1510047a,
  },
  chapters: {
    i2c1507187a: i2c1510047a_chapters,
    i2c1509134a: i2c1510047a_chapters,
    i2c1510047a: i2c1510047a_chapters,
  }
}


const fmtReply = (data) => ({ data, error: null })
const root = __dirname + '/public'
const koa = Koa()
const $ = (method, path, handler) => {
  koa.use(route[method](path, handler))
}

koa.use(bodyparser())


$('get', '/api/me', function * () {
  this.body = fmtReply(me)
})


$('post', '/api/evaluations', function * () {
  const { cids } = this.request.body
  const items = cids.map(id => db.evaluations[id])

  this.body = fmtReply({
    count: items.length,
    items,
  })
})


$('get', '/api/evaluations/:id', function * (id) {
  this.body = fmtReply(db.evaluations[id])
})


$('get', '/api/evaluations/:id/chapters/:cid', function * (id, cid) {
  this.body = fmtReply(db.chapters[id].filter(c => c.id == cid).shift())
})


$('get', '/*', function * () {
  const isSent = yield send(this, this.path, { root })

  if (!isSent) {
    yield send(this, 'index.html', { root })
  }
})


koa.listen(3000)