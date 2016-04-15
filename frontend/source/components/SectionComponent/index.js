import ChartMediaLaydown from '/components/ChartMediaLaydown'
import ChartSalesPerformance from '/components/ChartSalesPerformance'
import ChartPromotionalActivity from '/components/ChartPromotionalActivity'
import ChartAcquireNewCustomers from '/components/ChartAcquireNewCustomers'
import ChartAcquireNewCustomers from '/components/ChartGrowCustomerProductRange'
import ChartNonPurchase from '/components/ChartNonPurchase'
import ChartOfferSales from '/components/ChartOfferSales'
import ChartUnitsUplift from '/components/ChartUnitsUplift'
import TablePerformanceSamples from '/components/TablePerformanceSamples'
import SectionHTML from '/components/SectionHTML'
import SectionEditableComments from '/components/SectionEditableComments'
import SectionGallery from '/components/SectionGallery'
import SectionTimings from '/components/SectionTimings'
import SectionChannels from '/components/SectionChannels'
import SectionObjectives from '/components/SectionObjectives'
import SectionInfo from '/components/SectionInfo'
import { Component, B } from '/components/component.js'
import style from './style.css'


const List = ({ component }) => (
  B({ className: style.list }, component.items.map((item, key) => (
    B({ className: style.list_item, key }, item)
  )))
)


const Text = ({ component }) => (
  B({ className: style.text }, component.content)
)


export default Component({
  render () {
    const { component, isEditable, uploadPath, onSave } = this.props

    switch (component.type) {
      case 'chart_sales_performance':
        return SectionEditableComments({
          component,
          isEditable,
          uploadPath,
          onSave,
          content: ChartSalesPerformance({ component }),
        })
      break

      case 'chart_promotional_activity':
        return SectionEditableComments({
          component,
          isEditable,
          uploadPath,
          onSave,
          content: ChartPromotionalActivity({ component }),
        })
      break

      case 'chart_offer_sales':
        return SectionEditableComments({
          component,
          isEditable,
          uploadPath,
          onSave,
          content: ChartOfferSales({ component }),
        })
      break

      case 'chart_units_uplift':
        return SectionEditableComments({
          component,
          isEditable,
          uploadPath,
          onSave,
          content: ChartUnitsUplift({ component }),
        })
      break

      case 'chart_media_laydown':
        return ChartMediaLaydown({ component })
      break

      case 'chart_non_purchase':
        return ChartNonPurchase({ component })
      break
      
      case 'chart_acquire_new_customers':
        return ChartAcquireNewCustomers({ component })
      break
      
      case 'grow_customer_product_range':
        return ChartGrowCustomerProductRange({ component })
      break
      
      case 'table_performance_samples':
        return TablePerformanceSamples({ component })
      break

      case 'gallery':
        return SectionGallery({ component, isEditable, uploadPath, onSave })
      break

      case 'html':
        return SectionHTML({
          component,
          isEditable,
          uploadPath,
          onSave,
        })
      break

      case 'info':
        return SectionInfo({
          component,
          isEditable,
          uploadPath,
          onSave,
        })
      break

      case 'list_channels':
        return SectionChannels({ component })
      break

      case 'list_timings':
        return SectionTimings({ component })
      break

      case 'list_value':
        return SectionObjectives({ component })
      break

      case 'list':
        return List({ component })
      break

      case 'text':
        return Text({ component })
      break

      default:
        return B(`${component.type} - not implemented`)
    }
  }
})