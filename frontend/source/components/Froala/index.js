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
    editor.froalaEditor('html.set', content)

    this.editor = editor
  },
  componentDidMount () {
    this.initialize()
  },
  componentWillUnmount () {
    this.editor.froalaEditor('destroy')
  },
  render () {
    return B({ className: 'froala-editor', ref: 'container' })
  }
})