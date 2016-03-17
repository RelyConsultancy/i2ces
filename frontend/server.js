import fs from 'fs'
import Koa from 'koa'
import send from 'koa-send'
import route from 'koa-route'
import bodyparser from 'koa-bodyparser'
import csvParser from 'csv-parser'

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
  },
  csv: {
    1: 'ie_cat_context_data.csv',
    2: 'ie_promo_data.csv',
  }
}


const csv = (file) => ((done) => {
  const rows = []

  fs.createReadStream(`./samples/csv/${file}`)
    .pipe(csvParser())
    .on('data', row => rows.push(row))
    .on('end', () => {
      done(null, rows)
    })
})


const fmtReply = (data) => ({ data, error: null })


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


$('get', '/api/evaluations/:cid/dataset/:id', function * (cid, id) {
  const data = yield csv(db.csv[id])

  this.body = fmtReply(data.filter(i => i.campaign_id == cid))
})


$('get', '/api/evaluations/:cid', function * (cid) {
  this.body = fmtReply(db.evaluations[cid])
})


$('get', '/api/evaluations/:cid/chapters/:id', function * (cid, id) {
  this.body = fmtReply(db.chapters[cid].filter(c => c.id == id).shift())
})


$('post', '/api/evaluations/:cid/chapters/:id', function * (cid, id) {
  this.request.body.content.forEach((section) => {
    console.log('------------------------------------------------------------')
    section.content.forEach(component => console.log(component))
  })

  this.body = fmtReply({ ok: 1 })
})


$('get', '/*', function * () {
  const options = { root: `${__dirname}/public` }
  const isSent = yield send(this, this.path, options)

  if (!isSent) {
    yield send(this, 'index.html', options)
  }
})


koa.listen(3000)