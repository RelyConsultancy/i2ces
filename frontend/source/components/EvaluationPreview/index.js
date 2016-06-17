import { Component, B } from '/components/component.js'
import setComponent from '/components/EvaluationSection/setComponent.js'
import * as action from '/application/actions.js'
import http from '/application/http.js'
import { getURLQuery } from '/application/utils.js'
import { savePDFMarkers } from '/application/actions.js'
import Links from './links.js'
import PDFIntro from './pdfIntro.js'
import PDFOutro from './pdfOutro.js'
import PDF from './pdf.js'
import { fmtDocument, stringifyMarkers, parseMarkers } from './markers.js'
import style from './style.css'


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
      content.push(setComponent({ component, isPDF: true }))
    })
  })

  const chapterTitle = B({ className: style.cover_title }, chapter.title)
  const chapterCover = B({ className: style.cover }, chapterTitle)
  const components = B({ className: 'components' }, ...content)
  const attrs = { className: 'chapter', id: chapter.id }

  return B(attrs, chapterCover, components)
}


const Template = ({ evaluation, chapters, debug, orientation }) => {
  const intro = PDFIntro({ evaluation })
  const outro = PDFOutro({ evaluation })
  const content = B({ className: 'chapters' }, ...chapters.map(Chapter))
  const attrs = {
    className: style.pdf + (debug ? ' debug' : '') + (orientation === 'landscape' ? ' landscape' : ''),
  }

  return B(attrs, intro, content, outro)
}


export default Component({
  getInitialState () {
    return {
      evaluation: null,
      chapters: null,
      markers: null,
      debug: false,
      orientation: 'landscape',
      isPreview: false,
    }
  },
  save () {
    const { evaluation, markers } = this.state
    const pdf_markers = stringifyMarkers(markers)

    savePDFMarkers(pdf_markers, (data) => {
      window.location.hash = `/evaluations/${evaluation.cid}`
    })
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

    if (!evaluation && !chapters) {
      return B({ className: style.no_data }, 'Loading evaluation ...')
    }

    const preview = B({
      className: style.action,
      onClick: (event) => {
        this.setState({ isPreview: !this.state.isPreview })
      },
    }, isPreview ? 'Close' : 'Preview')

    const save = isPreview ? null : B({
      className: style.action,
      onClick: this.save,
    }, 'Save')

    const actions = B({ className: style.actions }, preview, save)
    const links = Links({ evaluation })

    const content = isPreview
                  ? PDF({ evaluation, markers })
                  : Template({ evaluation, chapters, debug })

    return B({ className: style.layout }, links, content, actions)
  }
})