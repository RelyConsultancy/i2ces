import { Component, B, Image, Link, A } from '/components/component.js'
import Loader from '/components/Loader'
import store from '/application/store.js'
import { isI2C } from '/application/actions.js'
import style from './style.css'


const Logo = () => (
  Link({ to: '/evaluations', className: style.logo })
)


const LogoSupplier = ({ user }) => {
  if (user.type != 'supplier' || !user.logo) return null

  const { logo } = user

  if (logo.path) {
    var css = { backgroundImage: `url(${ logo.path })` }
    const width = 100
    const height = 80

    if (logo.height < logo.width) {
      const ratio = logo.width / width
      const padding = (height / 2) - (logo.height / ratio / 2)

      css.backgroundPositionY = padding + 'px'
    }
    else {
      const ratio = logo.height / height
      const padding = (width / 2) - (logo.width / ratio / 2)

      css.backgroundPositionX = padding + 'px'
    }
  }
  else {
    var label = B({ className: style.logo_supplier_label }, logo.label)
    var css = { fontSize: logo.label.length < 13 ? '1.25em' : '' }
  }

  return B({ style: css, className: style.logo_supplier }, label)
}


const Navigation = ({ store }) => {
  const links = [
    Link({ to: '/faqs' }, 'FAQs'),
    A({ href: '/user/logout' }, 'Logout'),
  ]

  if (isI2C()) {
    links.unshift(
      A({ href: '/user' }, 'Users'),
      A({ href: '/organization/business_unit' }, 'Suppliers')
    )
  }

  return B({ className: style.links }, ...links)
}


const Dashboard = Component({
  displayName: 'Dashboard',
  class: true,
  render () {
    const { store, children } = this.props
    const { flag, navigation, user } = store

    const header = B(
      { className: style.dashboard_header },
      // network indicator
      flag.network && Loader({ className: style.loader }),
      LogoSupplier({ user }),
      Logo(),
      Navigation({ store })
    )

    const content = B({ className: style.dashboard_content }, children)

    return B({ className: style.dashboard }, header, content)
  }
})


export default store.sync('dashboard', Dashboard)