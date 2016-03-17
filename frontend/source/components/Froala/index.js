import $ from 'jquery'
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


const Froala = Component({
  initialize () {
    const element = this.refs.container
    const editor = froala(element)
    const { onChange, content, options } = this.props

    editor.on('froalaEditor.contentChanged', onChange)
    editor.on('froalaEditor.initialized', () => {
      hideLicenseWarning(element)
    })

    editor.froalaEditor(Object.assign({}, defaults, options))
    editor.froalaEditor('html.set', content)

    this.editor = editor
  },
  destroy () {
    this.editor.froalaEditor('destroy')
  },
  componentDidMount () {
    this.initialize()
  },
  componentWillUnmount () {
    this.destroy()
  },
  render () {
    return B({ className: 'froala-editor', ref: 'container' })
  }
})


export default Froala