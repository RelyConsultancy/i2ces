console.log('Started Phantom.js')

var system = require('system')
var Page = require('webpage')

var page = Page.create()
var url = system.args[1]
var filepath = system.args[2]
var headers = system.args[3]
var delay = system.args[4] || 0

var dpi = 72.0
var dpcm = dpi/2.05
var pageWidth = 29.7
var pageHeight = 21.0

// set request headers
var customHeaders = {}
var setHeader = function (string) {
  var kv = string.trim().split('=')
  customHeaders[kv[0]] = kv[1]
}
headers.split('|').forEach(setHeader)


page.settings.dpi = dpi
page.zoomFactor = 1
page.customHeaders = customHeaders

page.viewportSize = {
  width: Math.round(pageWidth * dpcm),
  height: Math.round(pageHeight * dpcm)
}

page.paperSize = {
  width: page.viewportSize.width + 'px',
  height: page.viewportSize.height + 'px',
  margin: '1cm'
}

page.open(url, function (status) {
  if (status == "fail") {
    page.close()
    phantom.exit(1)
    return
  }

  setTimeout(function () {
    page.render(filepath, { format: 'pdf', quality: 100 })

    console.log('Rendering complete')

    phantom.exit()
  }, delay)
})