import { Component, B } from '/component/component.js'


/*
  props: {
    blocks: 1,
    items: [],
  }
*/
const Grid = Component({
  render () {
    let { blocks, items } = this.props

    items = items.map((item, index) => (
      B({ key: index }, item)
    ))

    return B({ className: `grid-blocks-${blocks}` }, items)
  }
})


export default Grid