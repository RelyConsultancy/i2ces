import scrollIntoView from 'scroll-into-view'
import SectionComponent from '/components/SectionComponent'
import { Component, B, Link } from '/components/component.js'
import { getInitials, slugify } from '/application/utils.js'
import store from '/application/store.js'
import * as action from '/application/actions.js'
import style from './style.css'


const Navigation = ({ store, params }) => {
  const back = Link({
    to: `/evaluations/${params.cid}`,
    className: style.navigation_item,
  }, 'Back to Evaluation Dashboard')

  return B({ className: style.navigation }, back)
}

const Header = ({ evaluation }) => B(
  { className: style.header },
  evaluation.display_title
)

const Chapters = ({ store, chapter }) => {
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
      className: isActive ? style.chapter_active : style.chapter,
      key: index,
    }
    return Link(attrs, initials)
  })

  return B({ className: style.chapters }, links)
}


const Headings = ({ store, chapter, focusedSection, focusSection }) => {
  const { evaluation, chapter_palette } = store
  const color = chapter_palette[chapter.order - 1]
  const sections = chapter.content.filter(item => item.type == 'section')
  const isActive = (section) => (section.title == focusedSection.title)

  const title = B({ className: style.headings_title }, chapter.title)
  const links = sections.map((section, index) => B({
    className: isActive(section) ? style.headings_active : style.headings_link,
    onClick: (event) => {
      const element = document.getElementById(slugify(section.title))

      scrollIntoView(element, {
        time: 400,
        align:{ top: 0, left: 0 }
      })

      focusSection(section)
    },
  }, section.title))

  return B({ className: style.headings, style: { color } }, title, ...links)
}


const Sections = ({ store, chapter, focusedSection, focusSection }) => {
  const { evaluation } = store
  const { cid } = evaluation
  const byType = (i => i.type == 'section')

  const isEditable = action.isEditable()
  const uploadPath = `/api/images/${evaluation.cid}/${chapter.id}`
  const onSave = () => {
    action.updateChapter({ chapter, cid }, (data) => {
      console.info(`Evaluation ${cid}/${chapter.id} updated`, data)
    })
  }

  const sections = chapter.content.filter(byType).map((section) => {
    const id = slugify(section.title)
    const title = B({ className: style.section_title }, section.title)

    const components = section.content.map((component) => (
      SectionComponent({ component, isEditable, uploadPath, onSave })
    ))

    return B({ id, className: style.section }, title, ...components)
  })

  return B({ className: style.headings_content }, ...sections)
}


const EvaluationChapter = Component({
  class: true,
  load () {
    const { store, params } = this.props
    const { cid, id } = params
    const chapter = store.chapters_cache[id]

    if (!store.evaluation) {
      action.fetchEvaluation({ cid }, () => {
        action.fetchChapter({ cid, id })
      })
    }
    else if (!chapter) {
      action.fetchChapter({ cid, id })
    }
    else {
      action.setChapter(chapter)
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
        Header({ evaluation }),
        Navigation({ store, params }),
        Chapters({ store, chapter }),
        Headings({ store, chapter, focusedSection, focusSection }),
        Sections({ store, chapter, focusedSection })
      )
    }

    return B({ className: style.content }, content)
  }
})


export default store.sync('evaluation', EvaluationChapter)