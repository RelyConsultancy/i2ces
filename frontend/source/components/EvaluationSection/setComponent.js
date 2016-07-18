import List from '/components/SectionList'
import Info from '/components/SectionInfo'
import Comment from '/components/SectionComment'
import EditableHTML from '/components/SectionHTML'
import Timings from '/components/SectionTimings'
import Channels from '/components/SectionChannels'
import Objectives from '/components/SectionObjectives'

import ChartMediaLaydown from '/components/ChartMediaLaydown'
import ChartSalesPerformance from '/components/ChartSalesPerformance'
import ChartPromotionalActivity from '/components/ChartPromotionalActivity'
import ChartNonPurchase from '/components/ChartNonPurchase'
import ChartOfferSales from '/components/ChartOfferSales'
import ChartUnitsUplift from '/components/ChartUnitsUplift'
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

import TablePerformanceSamples from '/components/TablePerformanceSamples'
import TableOfferSales from '/components/TableOfferSales'


export default ({ component, editMode, uploadPath, isPDF }) => {
  switch (component.type) {
    case 'list':
      return List({ component })
    break

    case 'info':
      return Info({ component, uploadPath, editMode })
    break

    case 'comment':
      return Comment({ component, uploadPath, editMode })
    break

    case 'html':
      return EditableHTML({ component, editMode, uploadPath })
    break

    case 'timings':
      return Timings({ component })
    break

    case 'channels':
      return Channels({ component })
    break

    case 'objectives':
      return Objectives({ component })
    break

    case 'chart_sales_performance':
      return ChartSalesPerformance({ component, isPDF })
    break

    case 'chart_promotional_activity':
      return ChartPromotionalActivity({ component, isPDF })
    break

    case 'chart_offer_sales':
      return ChartOfferSales({ component, isPDF })
    break

    case 'chart_units_uplift':
      return ChartUnitsUplift({ component, isPDF })
    break

    case 'chart_media_laydown':
      return ChartMediaLaydown({ component, isPDF })
    break

    case 'chart_non_purchase':
      return ChartNonPurchase({ component, isPDF })
    break

    case 'chart_acquire_new_customers':
      return ChartAcquireNewCustomers({ component, isPDF })
    break

    case 'chart_grow_customer_product_range':
      return ChartGrowCustomerProductRange({ component, isPDF })
    break

    case 'chart_grow_share_of_category':
      return ChartGrowShareOfCategory({ component, isPDF })
    break

    case 'chart_retain_existing_customers':
      return ChartRetainExistingCustomers({ component, isPDF })
    break

    case 'chart_grow_total_units':
      return ChartGrowTotalUnits({ component, isPDF })
    break

    case 'chart_retain_lapsing_customers':
      return ChartRetainLapsingCustomers({ component, isPDF })
    break

    case 'chart_launch_new_product':
      return ChartLaunchNewProduct({ component, isPDF })
    break

    case 'chart_retain_new_customers_trialists':
      return ChartRetainNewCustomers({ component, isPDF })
    break

    case 'chart_grow_spend_per_existing_customer':
      return ChartGrowSpendPerExistingCustomer({ component, isPDF })
    break

    case 'chart_grow_frequency_of_share_per_customer':
      return ChartGrowFrequencyOfSharePerCustomer({ component, isPDF })
    break

    case 'chart_grow_awareness':
      return ObjectiveGrowAwareness({ component, uploadPath, editMode })
    break

    case 'table_performance_samples':
      return TablePerformanceSamples({ component, isPDF })
    break

    case 'table_offer_sales':
      return TableOfferSales({ component, isPDF })
    break

    default:
      return `${component.type} - not implemented`
  }
}