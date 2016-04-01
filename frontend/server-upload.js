import { resolve, extname } from 'path'
import cuid from 'cuid'
import busboy from 'busboy'
import copyTo from 'fs-cp'


/*
  options:
    path - path where the files will be stored
    request - HTTP request object
    limits - limits option set on busboy

  return:
    files - an array of saved files
    data - an object with
*/
export default function * (options) {
  let { request, limits, path } = options

  if (!path) {
    throw new Error('handleUpload - path not defined')
  }

  let thunk = (done) => {
    let files = []
    let data = {}

    let onFile = (field, stream, name, encoding, mime) => {
      name = decodeURIComponent(name)
      let filename = cuid() + extname(name)
      let destination = `${path}/${filename}`

      copyTo(stream, destination)

      files.push({ field, path, filename, name, encoding, mime })
    }

    let onField = (name, value) => {
      data[name] = value
    }

    let onLimit = () => {
      finish(new Error('Reach files limit'))
    }

    let finish = (error) => {
      parser.removeListener('file', onFile)
      parser.removeListener('field', onField)
      parser.removeListener('partsLimit', onLimit)
      parser.removeListener('filesLimit', onLimit)
      parser.removeListener('fieldsLimit', onLimit)
      parser.removeListener('error', finish)
      parser.removeListener('finish', finish)
      parser.removeListener('close', finish)

      // delay response to let file handlers close connections
      setTimeout(() => {
        done(error, { data, files })
      }, 50)
    }

    let parser = busboy({
      headers: request.headers,
      limits: limits,
    })

    parser.on('file', onFile)
    parser.on('field', onField)
    parser.on('partsLimit', onLimit)
    parser.on('filesLimit', onLimit)
    parser.on('fieldsLimit', onLimit)
    parser.on('error', finish)
    parser.on('finish', finish)
    parser.on('close', finish)

    request.pipe(parser)
  }

  return yield thunk
}