import { Component, B, HTML } from '/components/component.js'
import Froala from '/components/Froala'
import style from './style.css'


const Info = ({ component, isEditable, className, value }) => {
  const content = component[value] || ''

  if (isEditable) {
    return Froala({
      content,
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
      isEditable: false,
    }
  },
  render () {
    const { component, content, editable, onSave } = this.props
    const { isEditable } = this.state

    const info = Info({
      component,
      isEditable,
      value: 'info',
      className: style.info,
    })

    const comment = Info({
      component,
      isEditable,
      value: 'comment',
      className: style.comment,
    })

    const toggle = !editable ? null: B({
      className: style.toggle,
      onClick: () => {
        if (isEditable) onSave()
        this.setState({ isEditable: !isEditable })
      }
    }, isEditable ? 'Save' : 'Edit')

    return B({ className: style.component }, info, content, comment, toggle)
  }
})