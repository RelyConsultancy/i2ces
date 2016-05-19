import { Component, B } from '/components/component.js'
import PDFViewer from '/components/PDFViewer'
import { stringifyMarkers } from './markers.js'
import style from './style.css'
import http from '/application/http.js'


const loadPDF = ({ cid }, handler) => {
  const url = `/api/evaluations/${cid}/pdf/temporary`

  http('head', url, { raw: true }, (reply) => {
    if (reply.status == 202) {
      setTimeout(loadPDF, 1000, { cid }, handler)
    }
    else if (reply.status == 200) {
      http('get', url, { blob: true }, (blob) => {
        console.info('Temporary PDF loaded', blob)
        handler(blob)
      })
    }
    else {
      console.error('PDF generation pulling failed')
      console.info(reply)
    }
  })
}

export default Component({
  getInitialState () {
    return { isGenerating: true }
  },
  componentDidMount () {
    const { evaluation, markers } = this.props
    const strMarkers = stringifyMarkers(markers)
    const url = `/api/pdf/${evaluation.cid}/temporary?markers=${strMarkers}`

    // start PDF generation
    http('post', url, { raw: true }, (reply) => {
      if (reply.status == 200) {
        loadPDF({ cid: evaluation.cid }, (blob) => {
          this.setState({
            isGenerating: false,
            url: URL.createObjectURL(blob),
          })
        })
      }
      else {
        console.error('Initiate PDF generation failed')
        console.info(reply)
      }
    })
  },
  render () {
    const { evaluation, markers } = this.props
    const { isGenerating, url } = this.state

    if (isGenerating) {
      const msg = 'Generating the PDF, please wait...'

      return B({ style: { textAlign: 'center' } }, msg)
    }

    return PDFViewer({
      url: url,
      className: style.pdf_preview,
      headers: {
        // ORO header required
        'X-CSRF-Header': 1,
      }
    })
  }
})