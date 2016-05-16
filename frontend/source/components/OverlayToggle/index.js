import { Component, B } from '/components/component.js'
import style from './style.css'


export default Component({
  getInitialState () {
    return { on: this.props.on || false }
  },
  render () {
    const { label, position, onChange } = this.props
    const { on } = this.state

    const toggle = B({
      className: on ? style.toggle_on : style.toggle
    })

    const onClick = () => {
      this.setState({ isOn: !isOn })
      onChange(!isOn)
    }

    return B({ onClick, className: style.component }, before, toggle, after)
  }
})