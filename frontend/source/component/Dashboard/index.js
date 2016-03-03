import { Component, B, Image, Link } from '/component/component.js'
import Loader from '/component/Loader'
import store from '/application/store.js'
import style from './style.css'
import { setNetworkIndicator } from './actions.js'


const Logo = ({ image }) => (
  Link({ to: '/evaluations', className: style.logo }, Image({ src: image }))
)


const Navigation = ({ links }) => (
  B({ className: style.links }, links.map(
    link => Link({ key: link.path, to: link.path }, link.label)
  ))
)


const renderTopbar = (state, dispatch) => {
  const attrs = {
    className: style.topbar,
  }

  // network indicator
  const network = state.network ? Loader({ className: style.loader }) : null

  return B(
    attrs,
    network,
    Logo({ image: '/images/logo.png' }),
    Navigation({ links: state.navigation })
  )
}


const renderContent = (content) => {
  const attrs = {
    className: style.content,
  }

  return B(attrs, content)
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
      renderTopbar(store),
      renderContent(children)
    )
  }
})


export default store.sync('dashboard', Dashboard)