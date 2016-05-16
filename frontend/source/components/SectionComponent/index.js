import ChartMediaLaydown from '/components/ChartMediaLaydown'
import ChartSalesPerformance from '/components/ChartSalesPerformance'
import ChartPromotionalActivity from '/components/ChartPromotionalActivity'
import ChartAcquireNewCustomers from '/components/ChartAcquireNewCustomers'
import ChartGrowCustomerProductRange from '/components/ChartGrowCustomerProductRange'
import ChartGrowTotalUnits from '/components/ChartGrowTotalUnits'
import ChartGrowShareOfCategory from '/components/ChartGrowShareOfCategory'
import ChartRetainExistingCustomers from '/components/ChartRetainExistingCustomers'
import ChartRetainLapsingCustomers from '/components/ChartRetainLapsingCustomers'
import ChartLaunchNewProduct from '/components/ChartLaunchNewProduct'
import ChartRetainNewCustomers from '/components/ChartRetainNewCustomers'
import ChartGrowSpendPerExistingCustomer from '/components/ChartGrowSpendPerExistingCustomer'
import ChartGrowFrequencyOfSharePerCustomer from '/components/ChartGrowFrequencyOfSharePerCustomer'
import ObjectiveGrowAwareness from '/components/ObjectiveGrowAwareness'

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
          return SectionEditableComments({
            component,
            isEditable,
            uploadPath,
            onSave,
            content: ChartAcquireNewCustomers({ component }),
          })
      break
      
      case 'chart_grow_customer_product_range':
        return SectionEditableComments({
          component,
          isEditable,
          uploadPath,
          onSave,
          content: ChartGrowCustomerProductRange({ component }),
        })
      break
      
      case 'chart_grow_share_of_category':
        return SectionEditableComments({
          component,
          isEditable,
          uploadPath,
          onSave,
          content: ChartGrowShareOfCategory({ component }),
        })
      break
      
      case 'chart_retain_existing_customers':
        return SectionEditableComments({
          component,
          isEditable,
          uploadPath,
          onSave,
          content: ChartRetainExistingCustomers({ component }),
        })
      break
      
      case 'chart_grow_total_units':
        return SectionEditableComments({
          component,
          isEditable,
          uploadPath,
          onSave,
          content: ChartGrowTotalUnits({ component }),
        })
      break
      
      case 'chart_retain_lapsing_customers':
        return SectionEditableComments({
            component,
              isEditable,
              uploadPath,
              onSave,
              content: ChartRetainLapsingCustomers({ component }),
            })
      break
      
      case 'chart_launch_new_product':
          return SectionEditableComments({
            component,
              isEditable,
              uploadPath,
              onSave,
              content: ChartLaunchNewProduct({ component }),
            })
      break
      
      case 'chart_retain_new_customers_trialists':
          return SectionEditableComments({
            component,
              isEditable,
              uploadPath,
              onSave,
              content: ChartRetainNewCustomers({ component }),
            })
          break
      
      case 'chart_grow_spend_per_existing_customer':
          return SectionEditableComments({
            component,
              isEditable,
              uploadPath,
              onSave,
              content: ChartGrowSpendPerExistingCustomer({ component }),
            })
      break
      
      case 'chart_grow_frequency_of_share_per_customer':
          return SectionEditableComments({
            component,
              isEditable,
              uploadPath,
              onSave,
              content: ChartGrowFrequencyOfSharePerCustomer({ component }),
            })
      break
      
      case 'chart_grow_awareness':
          return SectionEditableComments({
            component,
              isEditable,
              uploadPath,
              onSave,
              content: ObjectiveGrowAwareness({ component }),
            })
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