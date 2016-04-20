import SectionComponent from '/components/SectionComponent'
import { Component, B, A, Link } from '/components/component.js'
import * as action from '/application/actions.js'
import { download } from '/application/http.js'
import { fmtDate } from '/application/utils.js'
import style from './style.css'


const Links = ({ evaluation }) => {
  let links = [
    Link({
      className: style.link,
      to: `/evaluations/${evaluation.cid}`,
    }, 'Back to Evaluation'),
  ]

  if (evaluation.has_pdf) {
    const url = `/api/evaluations/${evaluation.cid}/pdf`

    links.push(
      A({
        className: style.link,
        href: url,
        onClick: (event) => {
          event.preventDefault()
          download(url, `${evaluation.cid}.pdf`)
        },
      }, 'PDF')
    )
  }

  return B({ className: style.links }, ...links)
}


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


const Intro = ({ evaluation }) => {
  const channels = evaluation.channels.map(i => i.label).join(', ')
  const titleSize = evaluation.display_title.length > 40 ? '1.5em' : null
  const subtitleSize = channels.length > 60 ? '0.875em' : titleSize ? '1em' : null

  const title = B({
    className: style.splash_intro_title,
    style: { fontSize: titleSize },
  }, evaluation.display_title)

  const subtitle = B({
    className: style.splash_intro_subtitle,
    style: { fontSize: subtitleSize },
  }, channels)

  const titleWrap = B({ className: style.splash_intro_title_wrap }, title, subtitle)
  const titleBox = B({ className: style.splash_intro_title_box }, titleWrap)
  const dates = B({ className: style.splash_intro_date },
    fmtDate(evaluation.start_date),
    ' - ',
    fmtDate(evaluation.end_date)
  )

  return B({ className: style.splash_intro, key: 'intro' }, titleBox, dates)
}



const Outro = ({ evaluation }) => B(
  { className: style.splash_outro, key: 'outro' },
  B({ className: style.splash_title }, 'Thank you')
)


const isIntro = (id) => (id == 'intro')
const isOutro = (id) => (id == 'outro')


export default Component({
  load () {
    const { cid, id } =  this.props.params
    const chapters = []

    // load just the evaluation
    if (isIntro(id) || isOutro(id)) {
      action.fetchEvaluation({ cid }, (evaluation) => {
        this.setState({ evaluation })
      })
    }
    // load just a chapter
    else if (id) {
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
    // load all chapters
    else {
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
    const { cid, id } = this.props.params
    const { evaluation, chapters } = this.state
    const byOrder = (a, b) => (a.order > b.order)
    const isSplashPage = Boolean(id)

    let content = B({ className: style.no_data }, 'Loading evaluation ...')

    if (!evaluation) return content

    if (isIntro(id)) {
      content = Intro({ evaluation })
    }
    else if (isOutro(id)) {
      content = Outro({ evaluation })
    }
    else if (evaluation && chapters) {
      content = chapters.sort(byOrder).map((chapter) => (
        Chapter({ evaluation, chapter, isSplashPage })
      ))

      if (chapters.length > 1) {
        content.unshift(Intro({ evaluation }))
        content.push(Outro({ evaluation }))
      }
    }

    return B({ className: style.preview }, Links({ evaluation }), content)
  }
})