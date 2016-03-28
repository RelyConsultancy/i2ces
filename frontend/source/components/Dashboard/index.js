import { Component, B, Image, Link, A } from '/components/component.js'
import Loader from '/components/Loader'
import store from '/application/store.js'
import { isUser } from '/application/actions.js'
import style from './style.css'


const Logo = ({ image }) => (
  Link({ to: '/evaluations', className: style.logo }, Image({ src: image }))
)


const Navigation = ({ store }) => {
  const links = [
    Link({ to: '/faqs' }, 'FAQs'),
  ]

  if (isUser('i2c_employee')) {
    links.unshift(
      A({ href: '/user' }, 'Users'),
      A({ href: '/organization/business_unit' }, 'Suppliers')
    )
  }

  return B({ className: style.links }, ...links)
}


const Topbar = ({ store }) => {
  const { flag, navigation } = store

  // network indicator
  const loader = flag.network && Loader({ className: style.loader })
  const attrs = { className: style.topbar }

  return B(
    attrs,
    loader,
    Logo({ image: '/images/logo.png' }),
    Navigation({ store })
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