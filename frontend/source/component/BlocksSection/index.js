import { Component, B, Link, Textarea } from '/component/component.js'
import style from './style.css'


const BlocksSection = Component({
  getInitialState () {
    return { isEditable: false }
  },
  render () {
    const { component, editable, onSave } = this.props
    const { isEditable } = this.state

    const label = isEditable ? 'Save' : 'Edit'
    const toggle = B({
      className: style.toggle,
      onClick: () => {
        if (isEditable) {
          onSave && onSave()
        }

        this.setState({ isEditable: !isEditable })
      }
    }, label)

    const blocks = component.items.map((item, key) => {
      const title = B({ className: style.block_title }, item.label)
      let content = B({ className: style.block_content }, item.content)

      if (isEditable) {
        content = Textarea({
          className: style.block_edit,
          defaultValue: item.content,
          onChange: (event) => {
            item.content = event.target.value
          },
        })
      }

      return B({ className: style.block, key }, title, content)
    })

    return B({ className: style.blocks }, blocks, editable ? toggle : null)
  }
})


export default BlocksSection