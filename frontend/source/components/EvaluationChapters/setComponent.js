import ChartMediaLaydown from '/components/ChartMediaLaydown'
import ChartSalesPerformance from '/components/ChartSalesPerformance'
import ChartPromotionalActivity from '/components/ChartPromotionalActivity'
import ChartNonPurchase from '/components/ChartNonPurchase'
import ChartOfferSales from '/components/ChartOfferSales'
import ChartUnitsUplift from '/components/ChartUnitsUplift'
import TablePerformanceSamples from '/components/TablePerformanceSamples'
import SectionHTML from '/components/SectionHTML'
import SectionGallery from '/components/SectionGallery'
import SectionTimings from '/components/SectionTimings'
import SectionObjectives from '/components/SectionObjectives'
import SectionInfo from '/components/SectionInfo'
import SectionEditableComments from '/components/SectionEditableComments'
import { B } from '/components/component.js'
import style from './style.css'


const List = ({ component }) => (
  B({ className: style.section_list }, component.items.map((item, key) => (
    B({ className: style.section_list_item, key }, item)
  )))
)


const Text = ({ component }) => (
  B({ className: style.text }, component.content)
)


export default ({ component, editable, onSave }) => {
  switch (component.type) {
    case 'chart_sales_performance':
      return SectionEditableComments({
        component, editable, onSave,
        content: ChartSalesPerformance({ component }),
      })
    break

    case 'chart_promotional_activity':
      return SectionEditableComments({
        component, editable, onSave,
        content: ChartPromotionalActivity({ component }),
      })
    break

    case 'chart_offer_sales':
      return SectionEditableComments({
        component, editable, onSave,
        content: ChartOfferSales({ component }),
      })
    break

    case 'chart_units_uplift':
      return SectionEditableComments({
        component, editable, onSave,
        content: ChartUnitsUplift({ component }),
      })
    break

    case 'chart_media_laydown':
      return ChartMediaLaydown({ component })
    break

    case 'chart_non_purchase':
      return ChartNonPurchase({ component })
    break

    case 'table_performance_samples':
      return TablePerformanceSamples({ component })
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
}