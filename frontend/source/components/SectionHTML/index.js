import { Component, B, HTML } from '/components/component.js'
import Froala from '/components/Froala'
import style from './style.css'


export default Component({
  renderToggle () {
    const { isEditable, onSave } = this.props
    const { editMode } = this.state
    const label = editMode ? 'Save' : 'Edit'

    if (!isEditable) return null

    const onClick = () => {
      if (editMode) onSave()
      this.setState({ editMode: !editMode })
    }

    return B({ onClick, className: style.toggle }, label)
  },
  getInitialState () {
    return { editMode: false }
  },
  render () {
    const { uploadPath, component } = this.props
    const { editMode } = this.state
    const html = component.content || ''

    let content = HTML(html)

    if (editMode) {
      content = Froala({
        content: html,
        options: {
          imageUploadParam: 'image',
          imageUploadURL: uploadPath,
        },
        onChange: (e, editor, data) => {
          component.content = editor.html.get()
        },
      })
    }

    const attrs = {
      className: style.component + ' fr-view',
    }

    return B(attrs, content, this.renderToggle())
  }
})