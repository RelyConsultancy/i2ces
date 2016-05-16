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

import TablePerformanceSamples from '/components/TablePerformanceSamples'
import TableOfferSales from '/components/TableOfferSales'


export default ({ component, editMode, uploadPath }) => {
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
      return ChartSalesPerformance({ component })
    break

    case 'chart_promotional_activity':
      return ChartPromotionalActivity({ component })
    break

    case 'chart_offer_sales':
      return ChartOfferSales({ component })
    break

    case 'chart_units_uplift':
      return ChartUnitsUplift({ component })
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

    case 'table_offer_sales':
      return TableOfferSales({ component })
    break

    default:
      return `"${component.type}" - not implemented`
  }
}