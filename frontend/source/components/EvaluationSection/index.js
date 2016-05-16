import { Component, B } from '/components/component.js'
import { slugify } from '/application/utils.js'
import setComponent from './setComponent.js'
import style from './style.css'


export default Component({
  displayName: 'EvaluationSection',
  getInitialState () {
    return {
      editMode: false,
    }
  },
  render () {
    const { section, isEditable, onSave, uploadPath, className } = this.props
    const { editMode } = this.state
    const showToggle = (isEditable && section.access == 'editable')

    const toggle = !showToggle ? null : B({
      className: style.toggle,
      onClick: () => {
        if (editMode) onSave()
        this.setState({ editMode: !editMode })
      }
    }, editMode ? 'Save' : 'Edit')

    const title = B({ className: style.section_title }, section.title)

    const components = section.content.map((component) => (
      setComponent({ component, editMode, uploadPath })
    ))

    const attrs = {
      id: slugify(section.title),
      className: className || style.section,
    }
    return B(attrs, toggle, title, ...components)
  }
})