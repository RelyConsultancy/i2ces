import scrollTo from 'element-scroll-to'
import { Component, B, Link } from '/component/component.js'
import HTMLSection from '/component/HTMLSection'
import BlocksSection from '/component/BlocksSection'
import { getInitials, slugify } from '/application/utils.js'
import store from '/application/store.js'
import style from './style.css'
import {
  fetchEvaluation,
  fetchChapter,
  setChapterSection,
  setChapter,
} from '/application/actions.js'


const List = ({ component }) => (
  B({ className: style.section_list }, component.items.map((item, key) => (
    B({ className: style.section_list_item, key }, item)
  )))
)


const Text = ({ component }) => (
  B({ className: style.text }, component.content)
)


const isEditable = (cid) => {
  const { user } = store.getState().dashboard
  const cids = user.edit.map(i => i.cid)

  return ~cids.indexOf(cid)
}

const setComponent = ({ component, cid }) => {
  switch (component.type) {
    case 'html':
      return HTMLSection({ component, editable: isEditable(cid) })
    break

    case 'blocks':
      return BlocksSection({ component, editable: isEditable(cid) })
    break

    case 'list':
      return List({ component })
    break

    case 'text':
      return Text({ component })
    break

    default:
      return component.type
  }
}


const Links = ({ store, params }) => {
  const back = Link({
    to: `/evaluations/${params.cid}`,
    className: style.link,
  }, 'Back to Evaluation Dashboard')

  return B({ className: style.links }, back)
}


const Chapters = ({ store, chapter }) => {
  const { evaluation, chapter_palette } = store
  const selected = chapter

  const links = evaluation.chapters.map((chapter, index) => {
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


const getSections = (chapter) => (
  chapter.content.filter(item => item.type == 'section')
)


const Sections = ({ store, chapter, ctx }) => {
  const { evaluation, chapter_palette } = store
  const color = chapter_palette[chapter.order - 1]
  const sections = getSections(chapter)
  const isActive = (section) => (section.title == ctx.state.section.title)

  const title = B({ className: style.sections_title }, chapter.title)
  const links = sections.map((section, index) => B({
    className: isActive(section) ? style.sections_active : style.sections_link,
    key: index,
    onClick: (event) => {
      ctx.setState({ section })
      scrollTo(document.getElementById(slugify(section.title)))
    },
  }, section.title))

  const content = sections.map((section, index) => {
    const title = B({ className: style.section_title }, section.title)

    const components = section.content.map((component) => (
      setComponent({ component, cid: evaluation.cid })
    ))

    const attrs = {
      className: style.section,
      id: slugify(section.title),
      key: index,
    }
    return B(attrs, title, ...components)
  })

  return B(
    B({ className: style.sections, style: { color } }, title, links),
    B({ className: style.sections_content }, content)
  )
}


const EvaluationChapters = Component({
  class: true,
  load () {
    const { store, params } = this.props
    const { cid, id } = params
    const chapter = store.chapters_cache[id]

    if (!store.evaluation) {
      fetchEvaluation(cid)
      fetchChapter({ cid, id })
    }
    else if (!chapter) {
      fetchChapter({ cid, id })
    }
    else {
      setChapter(chapter)
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
    const { setState } = this
    const { store, params } = this.props
    const { section } = this.state
    const { evaluation } = store

    const chapter = store.chapters_cache[params.id]

    let content = B({ className: style.no_data }, store.chapter_empty)

    if (chapter) {
      content = B(
        Links({ store, params }),
        Chapters({ store, chapter }),
        Sections({ store, chapter, ctx: this })
      )
    }

    return B({ className: style.content }, content)
  }
})


export default store.sync('evaluation', EvaluationChapters)