import { Component, B } from '/components/component.js'
import style from './style.css'


export default Component({
  getInitialState () {
    return { isOn: this.props.isOn || false }
  },
  render () {
    const { label, position, onChange } = this.props
    const { isOn } = this.state

    const toggle = B({
      className: isOn ? style.toggle_on : style.toggle
    })

    const text = isOn ? label.on : label.off
    const before = label.position == 'left' ? text : null
    const after = before ? null : text

    const onClick = () => {
      this.setState({ isOn: !isOn })
      onChange(!isOn)
    }

    return B({ onClick, className: style.component }, before, toggle, after)
  }
})