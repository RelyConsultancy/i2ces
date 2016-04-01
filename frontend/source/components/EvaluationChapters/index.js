import scrollTo from 'element-scroll-to'
import { Component, B, Link } from '/components/component.js'
import { getInitials, slugify } from '/application/utils.js'
import store from '/application/store.js'
import * as $ from '/application/actions.js'
import setComponent from './setComponent.js'
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

  if (!evaluation) return null

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


const Headings = ({ store, chapter, focusedSection, focusSection }) => {
  const { evaluation, chapter_palette } = store
  const color = chapter_palette[chapter.order - 1]
  const sections = chapter.content.filter(item => item.type == 'section')
  const isActive = (section) => (section.title == focusedSection.title)

  const title = B({ className: style.sections_title }, chapter.title)
  const links = sections.map((section, index) => B({
    className: isActive(section) ? style.sections_active : style.sections_link,
    onClick: (event) => {
      focusSection(section)
      scrollTo(document.getElementById(slugify(section.title)))
    },
  }, section.title))

  return B({ className: style.sections, style: { color } }, title, ...links)
}


const Sections = ({ store, chapter, focusedSection, focusSection }) => {
  const { evaluation } = store
  const { cid } = evaluation
  const byType = (i => i.type == 'section')

  const isEditable = $.isEditable()
  const onSave = () => { $.updateChapter({ chapter, cid }) }

  const sections = chapter.content.filter(byType).map((section) => {
    const components = section.content.map((component) => (
      setComponent({ evaluation, chapter, component, isEditable, onSave })
    ))


    const title = B({ className: style.section_title }, section.title)
    const id = slugify(section.title)

    return B({ className: style.section, id }, title, ...components)
  })

  return B({ className: style.sections_content }, ...sections)
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

    if (store.evaluation && chapter) {
      const focusSection = (section) => { this.setState({ section }) }
      const focusedSection = this.state.section

      content = B(
        Navigation({ store, params }),
        ChapterLinks({ store, chapter }),
        Headings({ store, chapter, focusedSection, focusSection }),
        Sections({ store, chapter, focusedSection })
      )
    }

    return B({ className: style.content }, content)
  }
})


export default store.sync('evaluation', EvaluationChapters)