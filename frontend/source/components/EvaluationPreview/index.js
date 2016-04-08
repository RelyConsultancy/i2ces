import SectionComponent from '/components/SectionComponent'
import { Component, B, Link } from '/components/component.js'
import * as action from '/application/actions.js'
import style from './style.css'


const Chapter = ({ evaluation, chapter, isSplashPage }) => {
  const byType = (i => i.type == 'section')
  const isEditable = false

  const sections = chapter.content.filter(byType).map((section) => {
    const title = B({ className: style.section_title }, section.title)
    const pageBreak = section.page_break ? style.page_break : ''

    const components = section.content.map((component) => (
      SectionComponent({ component, isEditable })
    ))

    const className = `${style.section} ${pageBreak}`

    return B({ className }, title, ...components)
  })

  if (isSplashPage) {
    sections.unshift(B(
      { className: style.splash },
      B({ className: style.splash_title }, chapter.title)
    ))
  }

  return B({ className: style.chapter, key: chapter.id }, ...sections)
}


export default Component({
  load () {
    const { cid, id } =  this.props.params
    const chapters = []

    // load all chapters
    if (!id) {
      action.fetchEvaluation({ cid }, (evaluation) => {
        for (let chapter of evaluation.chapters) {
          action.fetchChapter({ cid, id: chapter.id }, (chapter) => {
            chapters.push(chapter)

            if (chapters.length == evaluation.chapters.length) {
              this.setState({ evaluation, chapters })
            }
          })
        }
      })
    }
    // load just a chapter
    else {
      action.fetchEvaluation({ cid }, (evaluation) => {
        for (let chapter of evaluation.chapters) {
          if (chapter.id == id) {
            action.fetchChapter({ cid, id: chapter.id }, (chapter) => {
              this.setState({ evaluation, chapters: [chapter] })
            })
          }
        }
      })
    }
  },
  getInitialState () {
    return { evaluation: null, chapters: null }
  },
  componentDidMount () {
    this.load()
  },
  componentDidUpdate ({ params }) {
    const { cid, id } =  this.props.params

    if (params.cid != cid || params.id != id) {
      this.load()
    }
  },
  render () {
    const { evaluation, chapters } = this.state
    const isSplashPage = Boolean(this.props.params.id)
    const byOrder = (a, b) => (a.order > b.order)

    let content = B({ className: style.no_data }, 'Loading evaluation ...')

    if (evaluation && chapters) {
      content = chapters.sort(byOrder).map((chapter) => (
        Chapter({ evaluation, chapter, isSplashPage })
      ))
    }

    return B({ className: style.preview }, content)
  }
})