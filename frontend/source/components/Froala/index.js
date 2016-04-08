import $ from 'jquery'
import assign from 'object-assign'
import { Component, B } from '/components/component.js'
import { findDOMNode } from 'react-dom'
import froala from './froala.js'


const hideLicenseWarning = (element) => {
  $(element).find('> div > a').each((i, el) => {
    if ($(el).text() == 'Unlicensed Froala Editor') {
      $(el).parent().css('z-index', '-1')
    }
  })
}


const defaults = {
  placeholderText: ' ',
  requestHeaders: {
    // ORO header required
    'X-CSRF-Header': 1,
  },
  fontFamily: {
    "'Avenir LT Std 45 Book'": 'Avenir',
    "'Archer Medium', serif": 'Archer',
    "'Archer Bold', serif": 'Archer Bold',
    "Arial,Helvetica,sans-serif": 'Arial',
    "Georgia,serif": 'Georgia',
    "Impact,Charcoal,sans-serif": 'Impact',
    "Tahoma,Geneva,sans-serif": 'Tahoma',
    "'Times New Roman',Times,serif": 'Times New Roman',
    "Verdana,Geneva,sans-serif": 'Verdana',
  },
}


export default Component({
  displayName: 'Froala',
  initialize () {
    const element = this.refs.container
    const editor = froala(element)
    const { onChange, content, options } = this.props

    editor.on('froalaEditor.contentChanged', onChange)
    editor.on('froalaEditor.initialized', () => {
      hideLicenseWarning(element)
    })

    editor.froalaEditor(assign({}, defaults, options))
    editor.froalaEditor('html.set', content || '')

    this.editor = editor
  },
  componentDidMount () {
    this.initialize()
  },
  componentWillUnmount () {
    this.editor.froalaEditor('destroy')
  },
  render () {
    const attrs = {
      className: 'froala-editor',
      style: this.props.style,
      ref: 'container',
    }

    return B(attrs)
  }
})