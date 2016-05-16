import { B, A, Link } from '/components/component.js'
import { download } from '/application/http.js'
import style from './style.css'


export default ({ evaluation }) => {
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