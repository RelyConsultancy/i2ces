import setMarkers from './setMarkers.js'
import style from './style.css'

export default Component({
  componentDidMount () {
    setMarkers()
  },
  render () {
    var count = (595/5)*25
    var lines = []

    for (var i = 0; i < count; ++i) {
      lines.push(B({ className: style.line }, (i+1) * 5))
    }

    return B({ className: style.preview }, B({ className: style.pdf }, ...lines))
  }
})
