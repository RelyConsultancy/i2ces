import { Component, B, Link } from '/components/component.js'
import ChartMediaLaydown from '/components/ChartMediaLaydown'
import ChartSalesPerformance from '/components/ChartSalesPerformance'
import ChartPromotionalActivity from '/components/ChartPromotionalActivity'
import ChartNonPurchase from '/components/ChartNonPurchase'
import TablePerformanceSamples from '/components/TablePerformanceSamples'
import SectionHTML from '/components/SectionHTML'
import SectionGallery from '/components/SectionGallery'
import SectionTimings from '/components/SectionTimings'
import SectionObjectives from '/components/SectionObjectives'
import SectionInfo from '/components/SectionInfo'
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


export default ({ store, chapter, selected, setSection }) => {
  if (!store.evaluation) return null

  const { cid } = store.evaluation
  const editable = isEditable(cid)
  const byType = (i => i.type == 'section')
  const onSave = () => { $.updateChapter({ chapter, cid }) }

  const sections = chapter.content.filter(byType).map((section) => {
    const components = section.content.map((component) => {
      switch (component.type) {
        case 'chart_media_laydown':
          return ChartMediaLaydown({ component, editable, onSave })
        break

        case 'chart_sales_performance':
          return ChartSalesPerformance({ component, editable, onSave })
        break

        case 'chart_promotional_activity':
          return ChartPromotionalActivity({ component, editable, onSave })
        break

        case 'table_performance_samples':
          return TablePerformanceSamples({ component })
        break

        case 'chart_non_purchase':
          return ChartNonPurchase({ component })
        break

        case 'gallery':
          return SectionGallery()
        break

        case 'html':
          return SectionHTML({ component, editable, onSave })
        break

        case 'list_timings':
          return SectionTimings({ component })
        break

        case 'list_value':
          return SectionObjectives({ component })
        break

        case 'info':
          return SectionInfo({ component, editable, onSave })
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