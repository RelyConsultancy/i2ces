import { Component, B, Link } from '/components/component.js'
import Section from '/components/EvaluationSection'
import * as action from '/application/actions.js'
import style from './style.css'


const Chapter = ({ evaluation, chapter }) => {
  const byType = (i => i.type == 'section')
  const isEditable = false

  const sections = chapter.content.filter(byType).map((section) => (
    Section({ section, isEditable })
  ))

  return B({ className: style.chapter }, ...sections)
}


export default Component({
  load () {
    const { cid } =  this.props.params
    const chapters = []

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
  },
  getInitialState () {
    return { evaluation: null, chapters: null }
  },
  componentDidMount () {
    this.load()
  },
  componentDidUpdate ({ params }) {
    if (this.props.params.cid != params.cid) this.load()
  },
  render () {
    const { evaluation, chapters } = this.state
    const byOrder = (a, b) => (a.order > b.order)

    let content = B({ className: style.no_data }, 'Loading evaluation ...')

    if (evaluation && chapters) {
      content = B(...chapters.sort(byOrder).map((chapter) => (
        Chapter({ evaluation, chapter })
      )))
    }

    return B({ className: style.preview }, content)
  }
})