import { resolve, join } from 'path'
import fs from 'fs'
import Koa from 'koa'
import send from 'koa-send'
import route from 'koa-route'
import bodyparser from 'koa-bodyparser'
import csvParser from 'csv-parser'
import handleUpload from './server_upload.js'


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


// setup Koa
const koa = Koa()
const $ = (method, path, handler) => {
  koa.use(route[method](path, handler))
}
koa.use(bodyparser())


// get active user
$('get', '/api/me', function * () {
  const file = `${__dirname}/samples/me.json`

  this.body = yield loadJSON(file)
})


// get evaluations list
$('post', '/api/evaluations', function * () {
  const { cids } = this.request.body
  const items = []

  for (let cid of cids) {
    items.push(yield loadJSON(`${__dirname}/samples/evaluation.${cid}.json`))
  }

  this.body = {
    count: items.length,
    items,
  }
})


// get evaluation
$('get', '/api/evaluations/:cid', function * (cid) {
  const file = `${__dirname}/samples/evaluation.${cid}.json`

  this.body = yield loadJSON(file)
})


// get evaluation chapter
$('get', '/api/evaluations/:cid/chapters/:id', function * (cid, id) {
  const file = `${__dirname}/samples/chapter.${cid}.${id}.json`

  this.body = yield loadJSON(file)
})


// update evaluation chapter
$('post', '/api/evaluations/:cid/chapters/:id', function * (cid, id) {
  this.request.body.content.forEach((section) => {
    console.log('------------------------------------------------------------')
    section.content.forEach(component => console.log(component))
  })

  this.body = { ok: 1 }
})


// get dataset
$('get', '/api/evaluations/:cid/dataset/:id', function * (cid, id) {
  const file = `${__dirname}/samples/dataset.${id}.json`

  this.body = yield loadJSON(file)
})


// evaluation publish / unpublish
$('post', '/api/evaluations/:cid/publish', function * (cid) {
  this.body = { ok: 1 }
})
$('post', '/api/evaluations/:cid/unpublish', function * (cid) {
  this.body = { ok: 1 }
})


// FAQ page
$('get', '/api/pages/*', function * () {
  const content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'

  this.body = { content, title: 'F.A.Q.' }
})
$('post', '/api/pages/*', function * () {
  console.info(this.request.body)

  this.body = { ok: 1 }
})


// upload images
$('post', '/api/images/*', function * () {
  const path = `/images/samples`
  const root = join(__dirname, '/public', path)

  const result = yield handleUpload({ path: root, request: this.req })

  if (!result || !result.files.length) {
    this.body = { error: { message: 'No files uploaded' } }
  }
  else {
    let { data } = result
    let files = []

    for (let file of result.files) {
      fs.renameSync(join(file.path, file.filename), join(root, file.name))

      files.push({
        path: join(path, file.name),
        name: file.name,
      })

      // return
      if (file.field == 'image') {
        return this.body = {
          link: join(path, file.name),
        }
      }
    }

    this.body = { files }
  }
})


// test incoming headers
$('get', '/api/headers', function * () {
  console.log(this.request.headers)

  this.body = this.request.headers
})


// test PDF generation and download
$('get', '/api/evaluations/:cid/pdf', function * () {
  yield send(this, 'phantomjs_export.pdf', { root: `${__dirname}/samples` })
})

$('post', '/api/pdf/permanent/:cid', function * () {
  this.body = { ok: 1 }
})

$('post', '/api/pdf/:cid/temporary', function * () {
  this.body = { ok: 1 }
})

$('get', '/api/evaluations/:cid/pdf/temporary', function * () {
  yield send(this, 'phantomjs_export.pdf', { root: `${__dirname}/samples` })
})
$('head', '/api/evaluations/:cid/pdf/temporary', function * () {
  // simulate workload for pulling
  this.status = Math.random() >= 0.5 ? 202 : 200
  this.body = { ok: 1 }
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