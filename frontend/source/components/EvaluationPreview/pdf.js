import { Component, B } from '/components/component.js'
import PDFViewer from '/components/PDFViewer'
import { stringifyMarkers } from './markers.js'
import style from './style.css'
import http from '/application/http.js'


const onPDFReady = ({ cid }, handler) => {
  const url = `/api/evaluations/${cid}/pdf/temporary`

  http('head', url, { raw: true }, (reply) => {
    if (reply.status == 202) {
      setTimeout(onPDFReady, 1000, { cid }, handler)
    }
    else if (reply.status == 200) {
      handler(reply)
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
    http('post', url, (reply) => {
      onPDFReady({ cid: evaluation.cid }, () => {
        this.setState({ isGenerating: false })
      })
    })
  },
  render () {
    const { evaluation, markers } = this.props
    const { isGenerating } = this.state

    if (isGenerating) {
      const msg = 'Generating the PDF, please wait...'

      return B({ style: { textAlign: 'center' } }, msg)
    }

    return PDFViewer({
      url: `/api/evaluations/${evaluation.cid}/pdf/temporary`,
      className: style.pdf_preview,
      headers: {
        // ORO header required
        'X-CSRF-Header': 1,
      }
    })
  }
})