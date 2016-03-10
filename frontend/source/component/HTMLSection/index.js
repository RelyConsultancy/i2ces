import { Component, B, Link } from '/component/component.js'
import Froala from '/component/Froala'
import style from './style.css'


const HTMLSection = Component({
  getInitialState () {
    return { isEditable: false }
  },
  render () {
    const { component } = this.props
    const { isEditable } = this.state
    const html = component.value

    const label = isEditable ? 'Save' : 'Edit'
    const toggle = B({
      className: style.toggle,
      onClick: () => {
        this.setState({ isEditable: !isEditable })
      }
    }, label)

    let content = B({ dangerouslySetInnerHTML: { __html: html } })

    if (isEditable) {
      content = Froala({
        content: html,
        onChange: (e, editor, data) => {
          component.value = editor.html.get()
        },
      })
    }

    return B({ className: style.component }, toggle, content)
  }
})


export default HTMLSection