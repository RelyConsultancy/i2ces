import { Component, B, HTML } from '/components/component.js'
import Froala from '/components/Froala'
import style from './style.css'


const Editor = ({ content, onChange, uploadPath, style }) => (
  Froala({
    style,
    content,
    onChange,
    options: {
      imageUploadParam: 'image',
      imageUploadURL: uploadPath,
    },
  })
)


export default Component({
  getInitialState () {
    return {
      editMode: false,
    }
  },
  render () {
    const { uploadPath, component, isEditable, onSave } = this.props
    const { editMode } = this.state

    if (editMode) {
      var info = Editor({
        uploadPath,
        content: component.info,
        onChange: (event, editor) => {
          component.info = editor.html.get()
        }
      })

      var comment = Editor({
        style: { fontFamily: '"Archer Medium"' },
        uploadPath,
        content: component.comment,
        onChange: (event, editor) => {
          component.comment = editor.html.get()
        }
      })
    }
    else {
      var info = !component.info ? null : B({
        className: style.info + ' fr-view',
      }, HTML(component.info))

      var comment = !component.comment ? null : B({
        className: style.comment + ' fr-view',
      }, HTML(component.comment))
    }

    const toggle = !isEditable ? null: B({
      className: style.toggle,
      onClick: () => {
        if (editMode) onSave()
        this.setState({ editMode: !editMode })
      }
    }, editMode ? 'Save' : 'Edit')

    return B({ className: style.component }, info, comment, toggle)
  }
})