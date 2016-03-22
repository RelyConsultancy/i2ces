import { Component, B } from '/components/component.js'
import { fmtHTML } from '/application/utils.js'
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
    const { component } = this.props
    const { isEditable } = this.state
    const html = component.content || ''
    const className = isEditable ? style.block : style.info

    // ignore empty strings
    if (!html) return null

    let content = fmtHTML(html)

    if (isEditable) {
      content = Froala({
        content: html,
        onChange: (e, editor) => {
          component.content = editor.html.get()
        },
      })
    }

    return B({ className }, content, this.renderToggle())
  }
})