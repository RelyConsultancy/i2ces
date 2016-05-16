import $ from 'jquery'
import { forEach } from '/application/utils.js'
import style from './style.css'


// A4 page height (landscape)
const pageHeight = 590
// page_break added padding
const pageBreakPadding = 32


export default ({ markers }) => {
  const handlePageBreaks = (event) => {
    let $node = $(event.target)
    let $component = null

    while (!$node.hasClass('components')) {
      // ignore click on chapter cover
      if ($node.hasClass(style.cover)) return

      $component = $node
      $node = $node.parent()
    }

    const chapterID = $node.parent().attr('id')

    if ($component.hasClass('page_break')) {
      removeMarker(chapterID, $component.index())
    }
    else {
      addMarker(chapterID, $component.index())
    }

    // set markers again
    setMarkers()
  }


  // markers => { chapterID: [componentIndex, componentIndex] }
  const addMarker = (chapterID, componentIndex) => {
    let pageBreaks = markers[chapterID]

    if (!pageBreaks) {
      pageBreaks = markers[chapterID] = []
    }

    pageBreaks.push(componentIndex)
  }

  const removeMarker = (chapterID, componentIndex) => {
    const pageBreaks = markers[chapterID]

    if (pageBreaks) {
      pageBreaks.splice(pageBreaks.indexOf(componentIndex), 1)
    }
  }


  const setPadding = (component, value) => {
    const oldValue = parseInt($(component).css('padding-bottom'))

    $(component)
      .addClass('is_modified')
      .css('padding-bottom', oldValue + value)
      .data('padding-bottom', oldValue)
  }

  const clearPadding = (component) => {
    $(component)
      .removeClass('is_modified')
      .css('padding-bottom', $(component).data('padding-bottom'))
      .data('padding-bottom', 0)
  }


  const clearFormat = () => {
    // remove page breaks
    $('.chapter').find('.components > div').removeClass('page_break')

    // reset component paddings
    $('.chapter').find('.is_modified').each((i, component) => {
      clearPadding(component)
    })
  }


  const setMarkers = () => {
    clearFormat()

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

        if (spaceOnPage < componentHeight || ~pageBreaks.indexOf(index)) {
          $component.addClass('page_break')

          // add padding to previous element to mark
          if (previous) {
            setPadding(previous, spaceOnPage)
          }

          // set spacing for new page and subtracting component
          spaceOnPage = pageHeight - (componentHeight - paddingTop) - pageBreakPadding
        }
        else {
          spaceOnPage -= componentHeight
        }

        if ($component.is(':last-child')) {
          setPadding($component, spaceOnPage)
        }

        previous = component
      })
    })
  }

  // attach handler for toggling page breaks on click
  $('.chapter').off('click').on('click', handlePageBreaks)

  setMarkers()

  // set phantomjs
  window.READY_TO_PRINT = true
}