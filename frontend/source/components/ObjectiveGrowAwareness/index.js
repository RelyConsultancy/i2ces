import { Component, B, HTML } from '/components/component.js'
import Froala from '/components/Froala'
import style from './style.css'


export default ({ component, uploadPath, editMode }) => {
  if (editMode) {
    return Froala({
      content: component.content,
      className: style.editor,
      options: {
        imageUploadParam: 'image',
        imageUploadURL: uploadPath,
      },
      onChange: (e, editor) => {
        component.content = editor.html.get()
      },
    })
  }
  // ignore empty strings
  else if (!component.content) {
    return null
  }
  else {
    const className = `${style.comment} fr-view`
    const comment = B({ className }, HTML(component.content))

    return B({ className: style.component }, comment)
  }
}