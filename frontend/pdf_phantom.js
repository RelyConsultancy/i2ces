var system = require('system')
var Page = require('webpage')

var url = system.args[1]
var filepath = system.args[2]
var headers = system.args[3]
var delay = system.args[4] || 0


var A4 = {
  width: 29.7,
  height: 21.0
}
var dpi = 72.0
var dpcm = dpi / 2.54
// pixel = (cm * dpi) / 2.54
var pageWidth = Math.round(A4.width * dpcm)
var pageHeight = Math.round(A4.height * dpcm)

console.log('Page size:', pageWidth + 'x' + pageHeight)


// set request headers
var customHeaders = {}

headers.split('`').forEach(function (string) {
  var kv = string.trim().split('~')
  customHeaders[kv[0]] = kv[1]
  console.log('Header: ' + kv[0] + '=' + kv[1])
})


var page = Page.create()

page.zoomFactor = 1
page.settings.dpi = 72.0
page.customHeaders = customHeaders

page.paperSize = {
  width: pageWidth + 'px',
  height: pageHeight + 'px',
  margin: 0
}

page.viewportSize = {
  width: page.paperSize.width,
  height: page.paperSize.height
}

page.open(url, function (status) {
  console.log('URL:', url)
  console.log('Open status:', status)

  if (status == "fail") {
    page.close()
    phantom.exit(1)
    return
  }

  setTimeout(function () {
    page.render(filepath, { format: 'pdf', quality: '100' })

    console.log('Rendered PDF to:', filepath)

    phantom.exit()
  }, delay)

  if (delay) {
    console.log('Renderer delayed by ' + delay + 'ms')
  }
})