import $ from 'jquery'
import store from '/application/store.js'
import { forEach } from '/application/utils.js'
import style from './style.css'


// A4 page height (landscape)
const pageHeight = 590
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


const setPadding = ($component, value) => {
  const oldValue = parseInt($component.css('padding-bottom'))

  $component
    .addClass('is_modified')
    .css('padding-bottom', oldValue + value)
    .data('padding-bottom', oldValue)
}


const setPageBreak = ($component, padding) => {
  $component.addClass('page_break')

  // add padding to previous element to occupy the space left on page
  if ($component.prev()) {
    setPadding($component.prev(), padding)
  }
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
      // set initial padding value
      .css('padding-bottom', $(component).data('padding-bottom'))
      .data('padding-bottom', 0)
  })


  $('.chapter').each((i, chapter) => {
    const $chapter = $(chapter)
    const chapterID = $chapter.attr('id')
    const pageBreaks = markers[chapterID] || []

    let spaceOnPage = pageHeight

    $chapter.find('.components > *').each((index, component) => {
      let componentHeight = component.offsetHeight
      const $component = $(component)
      const paddingTop = parseInt($component.css('padding-top'))
      const hasMarker = pageBreaks.indexOf(index) != -1
      const fitsOnSpaceLeft = componentHeight < spaceOnPage

      if (fitsOnSpaceLeft) {
        if (hasMarker) {
          setPageBreak($component, spaceOnPage)
          componentHeight = componentHeight - paddingTop + pageBreakPadding

          spaceOnPage = pageHeight - componentHeight
        }
        else {
          spaceOnPage = spaceOnPage - componentHeight
        }
      }
      else {
        // no space left on page, add it to the next one
        if (componentHeight < pageHeight) {
          setPageBreak($component, spaceOnPage)
          componentHeight = componentHeight - paddingTop + pageBreakPadding

          spaceOnPage = pageHeight - componentHeight
        }
        // no space left on page, add it to the next one and spread it across multiple pages
        else if (hasMarker) {
          setPageBreak($component, spaceOnPage)
          componentHeight = componentHeight - paddingTop + pageBreakPadding

          const scale = componentHeight / pageHeight % 1
          const spaceAfterScale = scale * pageHeight / 1

          spaceOnPage = pageHeight - spaceAfterScale
        }
        // component spreads across multiple pages
        else {
          const scale = (componentHeight - spaceOnPage) / pageHeight % 1
          const spaceAfterScale = scale * pageHeight / 1

          spaceOnPage = pageHeight - spaceAfterScale
        }
      }

      // add padding if its last component to fit a full page
      if ($component.is(':last-child')) {
        setPadding($component, spaceOnPage)
      }
    })
  })

  // set phantomjs flag
  setTimeout(() => {
    window.READY_TO_PRINT = true
  }, 1000)
}


export const fmtDocument = ({ markers }) => {
  // wait for all network requests to finish and format pdf
  const { network } = store.getState().dashboard.flag

  if (network) {
    setTimeout(fmtDocument, 1000, { markers })
  }
  else {
    setTimeout(setMarkers, 1000, { markers })
  }
}