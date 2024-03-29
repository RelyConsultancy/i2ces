import { Component, B, Element, Table, TR, TD } from '/components/component.js'
import Grid from '/components/Grid'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'
import numeral from 'numeral'
import _ from 'underscore'

const H3 = Element('h3')
// a factory function for the chart
const ChartRetainNewCustomers = (data, type) => {

  type = type || 'offer'

  const charts = {}

  charts.offer = {
      exposed: _.sortBy(data.charts.offer, 'timeperiod').map(i => i.exposed),
      control: _.sortBy(data.charts.offer, 'timeperiod').map(i => i.control)
  }

  charts.brand = {
      exposed: _.sortBy(data.charts.brand, 'timeperiod').map(i => i.exposed),
      control: _.sortBy(data.charts.brand, 'timeperiod').map(i => i.control)
  }

  // below is a C3 chart
  const chart = Chart({
    type: 'bar',
    data: {
      type: 'bar',
      x: 'Labels',
      colors: {
        'Control': '#A6A6A6',
        'Exposed': '#CB0270'
      },
      columns: [
        ['Control'].concat(charts[type].control),
        ['Exposed'].concat(charts[type].exposed),
        ['Labels', 'During', 'Post']
      ],
      labels: {
          format: (value) => {
              return numeral(value).format('0,0')
          }
      }
    },
    tooltip: {
        show: false
    },
    axis: {
      x: {
        type: 'category',
        categories: ['During', 'Post']
      },
      y: {
        label: {
          text: 'Number of new trialist customers',
          position: 'outer-middle'
        },
        tick: {
            format: (value) => {
                return numeral(value).format('0,0')
            }

        }
      },
    },
  })

  return chart
}

const TH = Element('th')

const TableMediaCombos = (data) => {
    const sumTotal = (metric) => {

        return _.reduce(_.pluck(data.table, metric), (memo, num) => { return memo + num }, 0)

    }
    const avgTotal = (metric) => {
        return (_.reduce(_.pluck(data.table, metric), (memo, num) => { return memo + num }, 0)) / data.table.length
    }

    const rows = [];

    rows.push(TR(TH('Channels and combinations'), TH('Number of households exposed'), TH({ className: 'highlighted' }, 'During campaign uplift'), TH('% uplift vs control')));

    _.each(data.table, (combo) => {
        rows.push(TR(TD(combo.media_type), TD(numeral(combo.exposed).format('0,0')), TD({ className: 'highlighted' }, numeral(combo.uplift).format('0,0')), TD(numeral(combo.percentage_uplift).format('0,0%'))))
    });

    rows.push(TR({ className: 'tr-totals'}, TD('Totals'), TD(numeral(sumTotal('exposed')).format('0,0')), TD({ className: 'highlighted' }, numeral(sumTotal('uplift')).format('0,0')), TD(numeral(avgTotal('percentage_uplift')).format('0,0%'))))

    return Table.apply(null, rows);


}

// boilerplate for React component and dataset fetching
export default Component({
  getInitialState () {
    return { data: [] }
  },
  componentDidMount () {
    const { source } = this.props.component

    fetchDataset(source, (data) => {

      this.setState({ data })
    })
  },
  render () {
    const { data } = this.state
    if ('charts' in data) {

        return Grid({
          blocks: 2,
          items: [
              B(
                H3({ className: 'i2c-chart-title' }, 'Offer'),
                B({ className: style.chart }, ChartRetainNewCustomers(data))
              ),
              B(
                H3({ className: 'i2c-chart-title' }, data.table.lenght > 1 ? 'During campaign uplift in new customers (trialists) returning, split by media channel combination' : 'Brand'),
                B({ className: data.table.lenght > 1 ? 'i2c-mc-table' : style.chart }, data.table.lenght > 1 ? TableMediaCombos(data) : ChartRetainNewCustomers(data, 'brand'))
              )
          ]
      })
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})