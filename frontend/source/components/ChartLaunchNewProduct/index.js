import { Component, B } from '/components/component.js'
import Chart from '/components/Chart'
import { fetchDataset } from '/application/actions.js'
import d3 from 'd3'
import style from './style.css'


// a factory function for the chart
const ChartLaunchNewProduct = (data) => {
  // format data
  const labels = data.map(i => i.label)
  const uplift = data.map(i => i.uplift)

  // below is a C3 chart
  const chart = Chart({
    type: 'bar',
    data: {
      x: 'labels',
      columns: [
        ['labels'].concat(labels),
        ['uplift'].concat(uplift),
      ],
      names: {
        uplift: 'Units uplift',
        percent: 'Weekly unit uplift/HH vs average',
      },
    },
    axis: {
      x: {
        type: 'category'
      },
      y: {
        label: {
          text: 'lorem ipsum sit dolor',
          position: 'outer-middle',
        },
      },
    },
  })

  return chart
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

    if (data.length) {
      return B({ className: style.chart }, ChartLaunchNewProduct(data))
    }
    else {
      return B({ className: style.loading }, 'Loading data ...')
    }
  }
})