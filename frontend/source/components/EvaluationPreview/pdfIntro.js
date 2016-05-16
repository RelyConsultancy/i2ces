import { Component, B } from '/components/component.js'
import { fmtDate } from '/application/utils.js'
import style from './style.css'


export default ({ evaluation }) => {
  const channels = evaluation.channels.map(i => i.label).join(', ')
  const titleSize = evaluation.display_title.length > 40 ? '1.5em' : null
  const subtitleSize = channels.length > 60 ? '0.875em' : titleSize ? '1em' : null

  const title = B({
    className: style.cover_intro_title,
    style: { fontSize: titleSize },
  }, evaluation.display_title)

  const subtitle = B({
    className: style.cover_intro_subtitle,
    style: { fontSize: subtitleSize },
  }, channels)

  const titleWrap = B({ className: style.cover_intro_title_wrap }, title, subtitle)
  const titleBox = B({ className: style.cover_intro_title_box }, titleWrap)
  const dates = B({ className: style.cover_intro_date },
    fmtDate(evaluation.date_start),
    ' - ',
    fmtDate(evaluation.date_end)
  )

  return B({ className: style.cover_intro, key: 'intro' }, titleBox, dates)
}