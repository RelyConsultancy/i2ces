import { writeFileSync } from 'fs'
import { dirname, filename } from  'path'
import browserify from 'browserify'
import watchify from 'watchify'
import babelify from  'babelify'
import uglify from 'uglify-js'
import gaze from 'gaze'
import CSSModules from '@carrd/css-modules'
import cssify from '@carrd/cssify'
import LiveReload from 'tiny-lr'
import autoprefixer from 'autoprefixer'
import postcss from 'postcss'


const getFile = (path) => (path.replace(root, ''))
const livereload = LiveReload()

livereload.listen(35729, () => {
  console.log('LiveReload listening on port 35729')
})

const browserReload = (path) => {
  livereload.changed({
    body: { files: [path] }
  })
  console.log(`Reload: ${path}`)
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
const bundleJS = (options) => {
  const root = options.root || dirname(options.input)
  const debug = options.debug === false ? false : true
  const { input, output } = options

  const onBundle = (error, bundle) => {
    if (error) return console.log(error.stack || error)

    if (!debug) {
      bundle = minifyJS(bundle.toString())
    }

    writeFileSync(output, bundle.toString())
    browserReload(output.replace(__dirname, ''))
  }

  const bundler = watchify(browserify({ cache: {}, packageCache: {} }), {
    ignoreWatch: ['**/*.css'],
  })

  bundler.transform(cssify(options.css))
  bundler.transform(babelify.configure({
    presets: ['es2015'],
    babelrc: false,
    resolveModuleSource: (source, filename) => (
      source = (source[0] == '/' ? root + source : source)
    )
  }))

  bundler.on('time', (time) => {
    console.log(`JS bundled: ${output.replace(__dirname, '')} ${time}ms`)
  })

  bundler.on('update', () => {
    bundler.bundle(onBundle)
  })

  bundler.add(input, { debug, basedir: root })
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
const bundleCSS = (options) => {
  const root = options.root || dirname(options.input)
  const debug = options.debug === false ? false : true
  const { input, output } = options
  const css = CSSModules()

  const bundle = () => {
    try {
      css.load(input)

      const handleCSS = (result) => {
        result.warnings().forEach((warn) => {
          console.warn(warn.toString())
        })

        writeFileSync(output, result.css)
      }

      const emitChange = () => {
        css.cache.clear()

        const path = output.replace(__dirname, '')
        browserReload(path)
        console.log(`CSS bundled: ${path}`)
      }

      postcss([autoprefixer])
        .process(css.stringify())
        .then(handleCSS)
        .then(emitChange)
    }
    catch (error) {
      console.log(error.stack)
    }
  }

  gaze(`${root}/**/*.css`, (error, watcher) => {
    if (error) return console.error(error)

    // initial build
    bundle()

    watcher.on('added', (path) => {
      console.log(`CSS watch: ${getFile(path)}`)
      bundle()
    })

    watcher.on('changed', (path) => {
      console.log(`CSS changed: ${getFile(path)}`)
      bundle()
    })
  })
}



// Bundle assets:

bundleCSS({
  input: `${__dirname}/source/index.css`,
  output: `${__dirname}/public/assets/bundle.css`,
})

bundleJS({
  input: `${__dirname}/source/index.js`,
  output: `${__dirname}/public/assets/bundle.js`,
})