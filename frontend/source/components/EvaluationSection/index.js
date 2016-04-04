import { Component, B } from '/components/component.js'
import { slugify } from '/application/utils.js'
import setComponent from './setComponent.js'
import style from './style.css'


export default Component({
  render () {
    const { section, isEditable, uploadPath, onSave } = this.props

    const components = section.content.map((component) => (
      setComponent({ component, isEditable, uploadPath, onSave })
    ))

    const title = B({ className: style.section_title }, section.title)
    const id = slugify(section.title)

    return B({ className: style.section, id }, title, ...components)
  }
})