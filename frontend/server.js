import fs from 'fs'
import Koa from 'koa'
import send from 'koa-send'
import route from 'koa-route'
import bodyparser from 'koa-bodyparser'
import csvParser from 'csv-parser'


// thunk function to load and parse JSON
const loadJSON = (file) => ((done) => {
  fs.readFile(file, 'utf8', (error, data) => {
    try {
      done(null, JSON.parse(data))
    }
    catch (error) {
      done(Error(`${file}: ${error.message}`))
    }
  })
})


// thunk function to load and parse CSV
const loadCSV = (file) => ((done) => {
  const rows = []

  fs.createReadStream(`./samples/csv/${file}`)
    .pipe(csvParser())
    .on('data', row => rows.push(row))
    .on('end', () => {
      done(null, rows)
    })
})


// format reply as Oro
const fmtReply = (data) => ({ data, error: null })


// setup Koa
const koa = Koa()
const $ = (method, path, handler) => {
  koa.use(route[method](path, handler))
}
koa.use(bodyparser())


// get active user
$('get', '/api/me', function * () {
  const file = `${__dirname}/samples/me.json`

  this.body = fmtReply(yield loadJSON(file))
})


// get evaluations list
$('post', '/api/evaluations', function * () {
  const { cids } = this.request.body
  const items = []

  for (let cid of cids) {
    items.push(yield loadJSON(`${__dirname}/samples/evaluation.${cid}.json`))
  }

  this.body = fmtReply({
    count: items.length,
    items,
  })
})


// get evaluation
$('get', '/api/evaluations/:cid', function * (cid) {
  const file = `${__dirname}/samples/evaluation.${cid}.json`

  this.body = fmtReply(yield loadJSON(file))
})


// get evaluation chapter
$('get', '/api/evaluations/:cid/chapters/:id', function * (cid, id) {
  const file = `${__dirname}/samples/chapter.${cid}.${id}.json`

  this.body = fmtReply(yield loadJSON(file))
})


// update evaluation chapter
$('post', '/api/evaluations/:cid/chapters/:id', function * (cid, id) {
  this.request.body.content.forEach((section) => {
    console.log('------------------------------------------------------------')
    section.content.forEach(component => console.log(component))
  })

  this.body = fmtReply({ ok: 1 })
})


// get dataset
$('get', '/api/evaluations/:cid/dataset/:id', function * (cid, id) {
  const file = `${__dirname}/samples/dataset.${cid}.${id}.json`

  this.body = fmtReply(yield loadJSON(file))
})


// get webapp index
$('get', '/*', function * () {
  const options = { root: `${__dirname}/public` }
  const isSent = yield send(this, this.path, options)

  if (!isSent) {
    yield send(this, 'index.html', options)
  }
})


koa.listen(3000)