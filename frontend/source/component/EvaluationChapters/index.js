import { Component, B, Link } from '/component/component.js'
import HTMLSection from '/component/HTMLSection'
import ListSection from '/component/ListSection'
import { getInitials } from '/application/utils.js'
import store from '/application/store.js'
import style from './style.css'
import {
  fetchEvaluation,
  fetchChapter,
  setChapterSection,
  setChapter,
} from '/application/actions.js'


const setComponent = ({ component }) => {
  switch (component.type) {
    case 'html':
      return HTMLSection({ component })
    break

    case 'list':
      return ListSection({ component })
    break

    default:
      return component.type
  }
}


const Navigation = ({ store, params }) => B(
  { className: style.links },
  Link({ to: `/evaluations/${params.cid}`, className: style.link }, 'Back to Evaluation Dashboard')
)


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
    },
  }, section.title))

  const content = sections.map((section, index) => {
    const title = B({ className: style.section_title }, section.title)

    const components = section.content.map((component) => (
      setComponent({ component })
    ))

    return B({ className: style.section, key: index }, title, ...components)
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
        Navigation({ store, params }),
        Chapters({ store, chapter }),
        Sections({ store, chapter, ctx: this })
      )
    }

    return B({ className: style.content }, content)
  }
})


export default store.sync('evaluation', EvaluationChapters)