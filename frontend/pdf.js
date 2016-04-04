'use strict'

const spawn = require('child_process').spawn
const args = process.argv.slice(2)

try {
  var electron = require('electron')
}
catch (e) {
  console.log('Launching electron ...')
}

if (!electron) {
  // add self to path
  args.unshift(__filename)

  const bin = require('electron-prebuilt')
  const child = spawn(bin, args)
  const log = (data) => {
    console.log(data.toString('utf8'))
  }

  child.stderr.on('data', log)
  child.stdout.on('data', log)
}
else {
  const fs = require('fs')
  const argv = require('yargs').argv

  // options
  const output = `${__dirname}/samples/export.pdf`
  const url = 'http://local:3000/#/preview/i2c1510047a'

  const toPDF = (error, data) => {
    if (error) return console.log(error)

    fs.writeFile(output, data, (error) => {
      if (error) return console.log(error)

      electron.app.quit()

      console.log(`PDF saved to: ${output}`)
    })
  }

  electron.app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
      electron.app.quit()
    }
  })

  electron.app.on('ready', (ctx) => {
    let window = new electron.BrowserWindow({
      width: 1000,
      height: 800,
      show: false,
    })

    window.on('close', (ctx) => {
      window = null
    })

    window.webContents.on('did-finish-load', (ctx) => {
      console.log('Loading web contents ...')

      const options = {
        pageSize: 'A4',
        // 0: default, 1: no margin, 2: minimum margin
        marginType: 2,
        printBackground: true,
        printSelectionOnly: false,
        landscape: false,
      }

      setTimeout(() => {
        window.webContents.printToPDF(options, toPDF)
      }, 2000)
    })

    window.loadURL(url, {
      // disable cache
      extraHeaders: 'pragma: no-cache\n',
    })
  })
}

