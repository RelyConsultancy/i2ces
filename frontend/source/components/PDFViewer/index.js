import { Component, B } from '/components/component.js'
import PDF from 'pdfjs-dist/build/pdf.combined.js'
import style from './style.css'


export default Component({
  displayName: 'PDFViewer',
  getInitialState () {
    return { isLoading: true }
  },
  loadPDF () {
    const { url, headers, onError } = this.props
    const container = document.getElementById('pdf_preview')

    const options = {
      url,
      httpHeaders: headers,
    }

    PDF.getDocument(options).then((data) => {
      this.setState({ isLoading: false })

      // no scrollbar if pdf has only one page
      if (data.numPages == 1) {
        container.style.overflowY = "hidden"
      }

      for (let i = 1; i <= data.numPages; i++) {
        data.getPage(i).then(function(page) {
          // calculate scale according to the box size
          const boxWidth = container.clientWidth
          const pdfWidth = page.getViewport(1).width
          const scale = boxWidth / pdfWidth
          const viewport = page.getViewport(scale)

          // set canvas for page
          const canvas = document.createElement('canvas')
          canvas.id  = `pdf_page_${i}`
          canvas.width  = viewport.width
          canvas.height = viewport.height
          container.appendChild(canvas)

          // get context and render page
          page.render({ viewport, canvasContext: canvas.getContext('2d') })
        })
      }
    }).catch((error) => {
      if (onError) onError(error)
      else console.warn('PDFViewer', error)
    })
  },
  componentDidMount () {
    this.loadPDF()
  },
  render () {
    const { isLoading } = this.state
    const { className } = this.props
    const pdf = B({ id: 'pdf_preview' })
    let loading = null

    if (isLoading) {
      loading = B({ className: style.loading },'PDF is loading...')
    }

    return B({ className: className || style.pdf }, pdf, loading)
  }
})