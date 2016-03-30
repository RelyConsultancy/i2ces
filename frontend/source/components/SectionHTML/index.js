import { Component, B, HTML } from '/components/component.js'
import Froala from '/components/Froala'
import style from './style.css'


export default Component({
  renderToggle () {
    const { editable, onSave } = this.props
    const { isEditable } = this.state
    const label = isEditable ? 'Save' : 'Edit'

    if (!editable) return null

    const onClick = () => {
      if (isEditable) onSave()
      this.setState({ isEditable: !isEditable })
    }

    return B({ onClick, className: style.toggle }, label)
  },
  getInitialState () {
    return { isEditable: false }
  },
  render () {
    const { cid, component } = this.props
    const { isEditable } = this.state
    const html = component.content || ''

    let content = HTML(html)

    if (isEditable) {
      content = Froala({
        content: html,
        options: {
          imageUploadParam: 'image',
          imageUploadURL: `/api/images/${cid}`,
        },
        onChange: (e, editor, data) => {
          component.content = editor.html.get()
        },
      })
    }

    return B({ className: style.component }, content, this.renderToggle())
  }
})