import { Component, B, HTML } from '/components/component.js'
import Froala from '/components/Froala'
import style from './style.css'


const Info = ({ uploadPath, component, editMode, className, value }) => {
  const content = component[value] || ''

  if (editMode) {
    return Froala({
      content,
      options: {
        imageUploadParam: 'image',
        imageUploadURL: uploadPath,
      },
      onChange: (e, editor) => {
        component[value] = editor.html.get()
      },
    })
  }
  // ignore empty strings
  else if (!content) {
    return null
  }
  else {
    return B({ className }, HTML(content))
  }
}


export default Component({
  getInitialState () {
    return {
      editMode: false,
    }
  },
  render () {
    const { uploadPath, component, content, isEditable, onSave } = this.props
    const { editMode } = this.state

    const info = Info({
      uploadPath,
      component,
      editMode,
      value: 'info',
      className: style.info,
    })

    const comment = Info({
      uploadPath,
      component,
      editMode,
      value: 'comment',
      className: style.comment,
    })

    const toggle = !isEditable ? null: B({
      className: style.toggle,
      onClick: () => {
        if (editMode) onSave()
        this.setState({ editMode: !editMode })
      }
    }, editMode ? 'Save' : 'Edit')

    return B({ className: style.component }, info, content, comment, toggle)
  }
})