import { Component, B } from '/components/component.js'
import PDFViewer from '/components/PDFViewer'
import setComponent from '/components/EvaluationSection/setComponent.js'
import * as action from '/application/actions.js'
import { getURLQuery } from '/application/utils.js'
import store from '/application/store.js'
import Links from './links.js'
import PDFIntro from './pdfIntro.js'
import PDFOutro from './pdfOutro.js'
import handleMarkers from './handleMarkers.js'
import style from './style.css'


const parseMarkers = (string) => {
  const markers = {}

  if (!string) return markers

  string.split('|').forEach((chapter) => {
    chapter = chapter.split(':')

    markers[chapter[0]] = chapter[1].split(',').map(i => parseInt(i.trim()))
  })

  return markers
}


const stringifyMarkers = (markers) => {
  let chapters = []

  Object.keys(markers).forEach((chapter) => {
    chapters.push(`${ chapter }:${ markers[chapter].join(',') }`)
  })

  return chapters.join('|')
}


const fmtDocument = ({ markers }) => {
  // wait for all network requests to finish and format pdf
  setTimeout(function onReady () {
    const { network } = store.getState().dashboard.flag

    network ? setTimeout(onReady, 500) : handleMarkers({ markers })
  }, 500)
}


const loadData = ({ cid }, handler) => {
  let chapters = []

  action.fetchEvaluation({ cid }, (evaluation) => {
    evaluation.chapters.forEach(({ id }) => {
      action.fetchChapter({ cid, id }, (chapter) => {
        chapters.push(chapter)

        // check if all chapters are loaded
        if (chapters.length == evaluation.chapters.length) {
          chapters = chapters.sort((a, b) => (a.order > b.order ? 1 : -1))

          handler({ evaluation, chapters })
        }
      })
    })
  })
}


const Chapter = (chapter) => {
  const bySection = (i => i.type == 'section')
  const content = []

  chapter.content.filter(bySection).forEach((section) => {
    content.push(
      B({ className: style.section_title }, section.title)
    )

    section.content.forEach((component) => {
      content.push(setComponent({ component }))
    })
  })

  const chapterTitle = B({ className: style.cover_title }, chapter.title)
  const chapterCover = B({ className: style.cover }, chapterTitle)
  const components = B({ className: 'components' }, ...content)
  const attrs = { className: 'chapter', id: chapter.id }

  return B(attrs, chapterCover, components)
}


const PDF = ({ evaluation, chapters, debug }) => {
  const intro = PDFIntro({ evaluation })
  const outro = PDFOutro({ evaluation })
  const content = B({ className: 'chapters' }, ...chapters.map(Chapter))
  const attrs = {
    className: style.pdf + (debug ? ' debug' : ''),
  }

  return B(attrs, intro, content, outro)
}


const Preview = ({ context }) => {

}


export default Component({
  getInitialState () {
    return {
      evaluation: null,
      chapters: null,
      markers: null,
      debug: false,
      isPreview: false,
    }
  },
  save (event) {
    console.log('saving...')
  },
  togglePreview () {
    this.setState({ isPreview: !this.state.isPreview })
  },
  componentDidMount () {
    const { cid } =  this.props.params
    const query = getURLQuery()
    const debug = query.debug

    loadData({ cid }, ({ evaluation, chapters }) => {
      const markers = parseMarkers(query.markers || evaluation.pdf_markers)

      this.setState({ evaluation, chapters, markers, debug })
    })
  },
  componentDidUpdate () {
    const { markers } = this.state

    // initiate PDF spacing format
    if (markers) {
      fmtDocument({ markers })
    }
  },
  render () {
    const { evaluation, chapters, markers, debug, isPreview } = this.state

    let content = B({ className: style.no_data }, 'Loading evaluation ...')

    if (isPreview) {
      const preview = PDFViewer({
        url: `/api/evaluations/${evaluation.cid}/pdf/temporary`,
        className: style.pdf_preview,
        headers: {
          // ORO header required
          'X-CSRF-Header': 1,
        }
      })

      const actions = B(
        { className: style.actions },
        B({ className: style.action, onClick: this.togglePreview }, 'Close')
      )

      content = B(preview, actions)
    }
    else if (evaluation && chapters) {
      const pdf = PDF({ evaluation, chapters, debug })

      const actions = B(
        { className: style.actions },
        B({ className: style.action, onClick: this.togglePreview }, 'Preview'),
        B({ className: style.action, onClick: this.save }, 'Save')
      )

      content = B(Links({ evaluation }), pdf, actions)
    }

    return B({ className: style.layout }, content)
  }
})