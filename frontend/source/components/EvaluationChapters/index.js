import scrollTo from 'element-scroll-to'
import { Component, B, Link } from '/components/component.js'
import { getInitials, slugify } from '/application/utils.js'
import store from '/application/store.js'
import * as $ from '/application/actions.js'
import Sections from './sections.js'
import style from './style.css'




const Navigation = ({ store, params }) => {
  const back = Link({
    to: `/evaluations/${params.cid}`,
    className: style.link,
  }, 'Back to Evaluation Dashboard')

  return B({ className: style.links }, back)
}


const ChapterLinks = ({ store, chapter }) => {
  const { evaluation, chapter_palette } = store
  const selected = chapter

  const byOrder = (a, b) => (a.order > b.order ? 1 : -1)

  const links = evaluation.chapters.sort(byOrder).map((chapter, index) => {
    const color = chapter_palette[chapter.order - 1]
    const isActive = chapter.id == selected.id

    const initials = B({
      className: style.chapter_initials,
      style: { backgroundColor: color },
    }, getInitials(chapter.title))

    const attrs = {
      to: `/evaluations/${evaluation.cid}/chapters/${chapter.id}`,
      className: isActive ? style.chapter_link_active : style.chapter_link,
      key: index,
    }
    return Link(attrs, initials)
  })

  return B({ className: style.chapter_links }, links)
}


const SectionHeadings = ({ store, chapter, selected, setSection }) => {
  const { evaluation, chapter_palette } = store
  const color = chapter_palette[chapter.order - 1]
  const sections = chapter.content.filter(item => item.type == 'section')
  const isActive = (section) => (section.title == selected.title)

  const title = B({ className: style.sections_title }, chapter.title)
  const links = sections.map((section, index) => B({
    className: isActive(section) ? style.sections_active : style.sections_link,
    onClick: (event) => {
      setSection(section)
      scrollTo(document.getElementById(slugify(section.title)))
    },
  }, section.title))

  return B({ className: style.sections, style: { color } }, title, ...links)
}


const EvaluationChapters = Component({
  class: true,
  load () {
    const { store, params } = this.props
    const { cid, id } = params
    const chapter = store.chapters_cache[id]

    if (!store.evaluation) {
      $.fetchEvaluation({ cid })
      $.fetchChapter({ cid, id })
    }
    else if (!chapter) {
      $.fetchChapter({ cid, id })
    }
    else {
      $.setChapter(chapter)
    }
  },
  getInitialState () {
    return {
      section: {}
    }
  },
  componentDidMount () {
    this.load()
  },
  componentDidUpdate ({ params }) {
    // when channel ID changes
    if (this.props.params.id != params.id) {
      this.load()
    }
  },
  render () {
    const { store, params } = this.props
    const { evaluation } = store
    const chapter = store.chapters_cache[params.id]

    let content = B({ className: style.no_data }, store.chapter_empty)

    if (chapter) {
      const setSection = (section) => { this.setState({ section }) }
      const selected = this.state.section

      content = B(
        Navigation({ store, params }),
        ChapterLinks({ store, chapter }),
        SectionHeadings({ store, chapter, selected, setSection }),
        Sections({ store, chapter, selected })
      )
    }

    return B({ className: style.content }, content)
  }
})


export default store.sync('evaluation', EvaluationChapters)