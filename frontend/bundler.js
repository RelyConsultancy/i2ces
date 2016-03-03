import { writeFileSync } from 'fs'
import { dirname, filename } from  'path'
import browserify from 'browserify'
import watchify from 'watchify'
import babelify from  'babelify'
import uglify from 'uglify-js'
import { watch } from 'chokidar'
import CSSModules from '@carrd/css-modules'
import cssify from '@carrd/cssify'
import LiveReload from 'tiny-lr'


const livereload = LiveReload()

livereload.listen(35729, () => {
  console.log('LiveReload listening on port 35729')
})

const browserReload = (path) => {
  livereload.changed({
    body: { files: [path] }
  })
  console.log(`LiveReloaded ${path}`)
}



const minifyJS = (code) => {
  const options = {
    fromString: true,
    mangle: true,
    compress: {
      sequences: true,
      dead_code: true,
      conditionals: true,
      booleans: true,
      unused: true,
      if_return: true,
      join_vars: true,
      drop_console: true,
    }
  }

  return uglify.minify(code, options).code
}


/*
  options {
    input: 'index.js',
    output: 'bundle.js',
    debug: true,
    root: '',
  }
*/
const bundleJS = (options, callback) => {
  const root = options.root || dirname(options.input)
  const debug = options.debug === false ? false : true

  const onBundle = (error, bundle) => {
    if (error) return console.log(error.stack)

    if (!debug) {
      bundle = minifyJS(bundle.toString())
    }

    if (callback) callback(options.output)

    writeFileSync(options.output, bundle)
  }

  const bundler = watchify(browserify(watchify.args))

  bundler.transform(cssify(options.css))
  bundler.transform(babelify.configure({
    presets: ['carrd'],
    babelrc: false,
    resolveModuleSource: (source, filename) => (
      source = (source[0] == '/' ? root + source : source)
    )
  }))

  bundler.on('time', (time) => {
    console.log(`JS bundle: ${time}`)
  })

  bundler.on('update', () => {
    bundler.bundle(onBundle)
  })

  bundler.add(options.input, {
    basedir: root,
    debug,
  })

  bundler.bundle(onBundle)
}


/*
  options {
    input: 'index.css',
    output: 'bundle.css',
    debug: true,
    root: '',
  }
*/
const bundleCSS = (options, callback) => {
  const root = options.root || dirname(options.input)
  const debug = options.debug === false ? false : true
  const modules = CSSModules()
  const watcher = watch(`${root}/**/*.css`)

  const save = () => {
    modules.load(options.input)
    const css = modules.stringify()

    writeFileSync(options.output, css)
    console.log(`CSS bundled ${options.output}`)

    if (callback) callback(options.output)
  }

  watcher.on('add', (file) => {
    console.log(`CSS file ${file} added.`)
  })
  watcher.on('change', save)
  watcher.on('ready', save)
}



// Bundle assets:

bundleJS({
  input: `${__dirname}/source/index.js`,
  output: `${__dirname}/public/assets/bundle.js`,
}, browserReload)

bundleCSS({
  input: `${__dirname}/source/index.css`,
  output: `${__dirname}/public/assets/bundle.css`,
}, browserReload)