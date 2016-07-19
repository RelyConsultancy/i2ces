import d3 from 'd3'
import { Component, B, Table, TR, TD } from '/components/component.js'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import { fmtUnit } from '/application/utils.js'
import style from './style.css'


const ChartUplift = ({ data }) => {
  const labels = data.map(i => i.channels)
  const uplift = data.map(i => i.uplift)
  const percent = data.map(i => i.percentage_uplift)
  const exposed = data.map(i => (i.uplift / i.exposed) * 1000)
  
  console.log(exposed)
  
  const chart = Chart({
    type: 'bar',
    tooltip: {
      show: false,
    },
    padding: {
      top: 20,
      bottom: 0,
    },
    color: {
      pattern: ['#D2E06C', '#EF7D46'],
    },
    data: {
      types: {
        percent: 'line',
        uplift: 'bar',
      },
      x: 'labels',
      columns: [
        ['labels'].concat(labels),
        ['uplift'].concat(uplift),
        ['percent'].concat(exposed),
      ],
      axes: {
        percent: 'y2',
      },
      names: {
        uplift: 'Units uplift',
        percent: 'Unit uplift per 1000 HH',
      },
      labels: {
        format: {
            uplift: (value) => {
                    if (value < 1) {
                      return value.toFixed(1)
                    }
                    else {
                      return (value / 1000).toFixed(1) + 'k'
                    }
            },
            percent: (value) => {
                    if (value < 1000) {
                      return value.toFixed(1)
                    }
                    else {
                      return (value / 1000).toFixed(1) + 'k'
                    }
            },    
        },
      },
    },
    axis: {
      x: {
        type: 'category'
      },
      y: {
        tick: {
          format: (value) => { 
            if (value > 999) { return (value / 1000).toFixed(1) + 'k' } else { return value } 
          }
        },
        label: {
          text: 'Offer Units Uplift',
          position: 'outer-middle',
        },
      },
      y2: {
        show: true,
        tick: {
          format: (value) => value.toFixed(1),
        },
        label: {
          text: 'Unit uplift per 1000 HH',
          position: 'outer-middle',
        },
      },
    },
  })

  return B({ className: style.chart }, chart)
}


const TableExposed = ({ data }) => {
  data = data.map(i => i.exposed)

  const width = (100 / data.length) + '%'
  const info = B({ className: style.table_data }, data.map((item, key) => (
    B({ key, style: { width } }, parseInt(item).toLocaleString())
  )))

  const label = B({ className: style.table_label }, 'Households Exposed')

  return B({ className: style.table }, label, info)
}


export default Component({
  loadData () {
    const { source } = this.props.component
    
    if (this.isMounted()) {
        fetchDataset(source, (data) => {
          this.setState({ data })
        })
    }
  },
  getInitialState () {
    return {
      data: [],
    }
  },
  componentDidMount () {
    this.loadData()
  },
  render () {
    const { data } = this.state

    if (data.length) {
      return B(
        ChartUplift({ data }),
        TableExposed({ data })
      )
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})