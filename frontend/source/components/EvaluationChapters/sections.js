import { Component, B, Link } from '/components/component.js'
import SectionHTML from '/components/SectionHTML'
import SectionBlocks from '/components/SectionBlocks'
import SectionSalesPerformance from '/components/SectionSalesPerformance'
import SectionPromotionalActivity from '/components/SectionPromotionalActivity'
import SectionGallery from '/components/SectionGallery'
import { slugify } from '/application/utils.js'
import store from '/application/store.js'
import * as $ from '/application/actions.js'
import style from './style.css'


const List = ({ component }) => (
  B({ className: style.section_list }, component.items.map((item, key) => (
    B({ className: style.section_list_item, key }, item)
  )))
)


const Text = ({ component }) => (
  B({ className: style.text }, component.content)
)


const isEditable = (cid) => {
  const { user } = store.getState().dashboard
  const cids = user.edit.map(i => i.cid)

  return ~cids.indexOf(cid)
}


const Sections = ({ store, chapter, selected, setSection }) => {
  const { cid } = store.evaluation
  const editable = isEditable(cid)
  const byType = (i => i.type == 'section')
  const onSave = () => { $.updateChapter({ chapter, cid }) }

  const sections = chapter.content.filter(byType).map((section) => {
    const components = section.content.map((component) => {
      switch (component.type) {
        case 'discrete_bar_chart':
          return SectionSalesPerformance({ component, editable, onSave })
        break

        case 'multi_bar_chart':
          return SectionPromotionalActivity({ component, editable, onSave })
        break

        case 'gallery':
          return SectionGallery()
        break

        case 'html':
          return SectionHTML({ component, editable, onSave })
        break

        case 'blocks':
          return SectionBlocks({ component, editable, onSave })
        break

        case 'list':
          return List({ component })
        break

        case 'text':
          return Text({ component })
        break

        default:
          return component.type
      }
    })

    const title = B({ className: style.section_title }, section.title)
    const id = slugify(section.title)

    return B({ className: style.section, id }, title, ...components)
  })

  return B({ className: style.sections_content }, ...sections)
}


export default Sections