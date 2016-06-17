import $ from 'jquery'
import store from '/application/store.js'
import { forEach } from '/application/utils.js'
import style from './style.css'

const orientation = 'portrait';

const pageHeight = 590

if (orientation === 'portrait') {
    pageHeight = 842
}
// A4 page height (landscape)


// page_break added padding
const pageBreakPadding = 32


export const parseMarkers = (string) => {
  const markers = {}

  if (!string) return markers

  string.split('|').forEach((chapter) => {
    chapter = chapter.split(':')

    markers[chapter[0]] = chapter[1].split(',').map(i => parseInt(i.trim()))
  })

  return markers
}


export const stringifyMarkers = (markers) => {
  let chapters = []

  Object.keys(markers).forEach((chapter) => {
    chapters.push(`${ chapter }:${ markers[chapter].join(',') }`)
  })

  return chapters.join('|')
}


const setPadding = (component, value) => {
  const oldValue = parseInt($(component).css('padding-bottom'))

  $(component)
    .addClass('is_modified')
    .css('padding-bottom', oldValue + value)
    .data('padding-bottom', oldValue)
}


export const setMarkers = ({ markers }) => {
  const handlePageBreaks = (event) => {
    let $node = $(event.target)
    let $component = null

    while (!$node.hasClass('components')) {
      // ignore click on chapter cover
      if ($node.hasClass(style.cover)) return

      $component = $node
      $node = $node.parent()
    }

    // ignore external clicks
    if (!$component) return

    const id = $node.parent().attr('id')
    const index = $component.index()

    // remove marker
    if ($component.hasClass('page_break')) {
      const pageBreaks = markers[id]

      if (pageBreaks) {
        pageBreaks.splice(pageBreaks.indexOf(index), 1)
      }
    }
    // add marker
    else {
      if (markers[id]) {
        markers[id].push(index)
      }
      else {
        markers[id] = [index]
      }
    }

    // update markers
    setMarkers({ markers })
  }


  // attach handler for toggling page breaks on click
  $('.chapter').off('click').on('click', handlePageBreaks)

  // remove page breaks
  $('.chapter').find('.components > div').removeClass('page_break')

  // reset component paddings
  $('.chapter').find('.is_modified').each((i, component) => {
    $(component)
      .removeClass('is_modified')
      .css('padding-bottom', $(component).data('padding-bottom'))
      .data('padding-bottom', 0)
  })


  $('.chapter').each((i, chapter) => {
    const $chapter = $(chapter)
    const chapterID = $chapter.attr('id')
    const pageBreaks = markers[chapterID] || []

    let spaceOnPage = pageHeight
    let previous = null

    $chapter.find('.components > *').each((index, component) => {
      const $component = $(component)
      const componentHeight = component.offsetHeight
      const paddingTop = parseInt($component.css('padding-top'))
      const paddingBottom = parseInt($component.css('padding-bottom'))

      const addPageBreak = () => {
        $component.addClass('page_break')

        // add padding to previous element to mark
        if (previous) {
          setPadding(previous, spaceOnPage)
        }

        // set spacing for new page and subtracting component
        spaceOnPage = pageHeight - (componentHeight - paddingTop) - pageBreakPadding
      }


      if (~pageBreaks.indexOf(index)) {
        addPageBreak()
      }
      else {
        if (componentHeight > spaceOnPage) {
          if (componentHeight > pageHeight) {
            const height = componentHeight - spaceOnPage
            const scale = componentHeight / pageHeight % 1

            spaceOnPage = pageHeight - (scale * pageHeight / 1)
          }
          else {
            addPageBreak()
          }
        }
        else {
          spaceOnPage -= componentHeight
        }
      }


      if ($component.is(':last-child')) {
        setPadding($component, spaceOnPage)
      }

      previous = component
    })
  })

  // set phantomjs flag
  setTimeout(() => {
    window.READY_TO_PRINT = true
  }, 250)
}


export const fmtDocument = ({ markers }) => {
  // wait for all network requests to finish and format pdf
  const { network } = store.getState().dashboard.flag

  if (network) {
    return setTimeout(setMarkers, 1250, { markers })
  }
}