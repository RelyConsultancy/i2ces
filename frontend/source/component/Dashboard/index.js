import { Component, B, Image, Link } from '/component/component.js'
import Loader from '/component/Loader'
import store from '/application/store.js'
import { setNetworkIndicator } from '/application/actions.js'
import style from './style.css'


const Logo = ({ image }) => (
  Link({ to: '/evaluations', className: style.logo }, Image({ src: image }))
)


const Navigation = ({ links }) => (
  B({ className: style.links }, links.map(
    link => Link({ key: link.path, to: link.path }, link.label)
  ))
)


const Topbar = ({ store }) => {
  // network indicator
  const network = store.network ? Loader({ className: style.loader }) : null
  const attrs = { className: style.topbar }

  return B(
    attrs,
    network,
    Logo({ image: '/images/logo.png' }),
    Navigation({ links: store.navigation })
  )
}


const Dashboard = Component({
  displayName: 'Dashboard',
  class: true,
  render () {
    const { store, children } = this.props

    const attrs = {
      className: style.component,
    }

    return B(
      attrs,
      Topbar({ store }),
      B({ className: style.content}, children)
    )
  }
})


export default store.sync('dashboard', Dashboard)