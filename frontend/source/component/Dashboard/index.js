import { Component, B, Image, Link } from '/component/component.js'
import Loader from '/component/Loader'
import store from '/application/store.js'
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
  const { flag, navigation } = store

  // network indicator
  const loader = flag.network && Loader({ className: style.loader })
  const attrs = { className: style.topbar }

  return B(
    attrs,
    loader,
    Logo({ image: '/images/logo.png' }),
    Navigation({ links: navigation })
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