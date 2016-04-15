var system = require('system')
var Page = require('webpage')

var url = system.args[1]
var filepath = system.args[2]
var headers = system.args[3]
var delay = system.args[4] || 0

var dpi = 72.0
var dpcm = dpi/2.05
var pageWidth = 29.7
var pageHeight = 21.0
var customHeaders = {}


// set request headers
headers.split('`').forEach(function (string) {
  var kv = string.trim().split('~')
  customHeaders[kv[0]] = kv[1]
})


// log headers
Object.keys(customHeaders).forEach(function (key) {
  console.log('Header:', key + '=' + customHeaders[key])
})
console.log('Open:', url)


var page = Page.create()

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
  console.log('Open status:', status)

  if (status == "fail") {
    page.close()
    phantom.exit(1)
    return
  }

  setTimeout(function () {
    page.render(filepath, { format: 'pdf', quality: 100 })

    console.log('Rendered PDF to:', filepath)

    phantom.exit()
  }, delay)

  if (delay) {
    console.log('Renderer delayed by ' + delay + 'ms')
  }
})