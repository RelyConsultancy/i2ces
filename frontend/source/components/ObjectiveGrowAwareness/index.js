import { Component, B, Element, Table, TR, TD } from '/components/component.js'
import { fetchDataset } from '/application/actions.js'
import style from './style.css'
import numeral from 'numeral'
import _ from 'underscore'

export default Component({
  loadData () {
    const { source } = this.props.component

    fetchDataset(source, (data) => {
      this.setState({ data })
    })
  },
  getInitialState () {
    return {
      data: null,
    }
  },
  componentDidMount () {
    this.loadData() 
  },
  render () {
    const { data } = this.state

    if (!data) return null

    const samples = TableSamples({ data: data.sku_details })
    const uplift = TableUplift({ data: data.incremental_uplift })

    return B({ className: style.component }, samples, uplift)
  }
})