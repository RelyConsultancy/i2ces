import Dashboard from '/component/Dashboard'
import Evaluations from '/component/Evaluations'
import Evaluation from '/component/Evaluation'
import FAQ from '/component/FAQ'


const routes = {
  path: '/',
  component: Dashboard,
  childRoutes: [{
    path: 'evaluations',
    component: Evaluations,
  }, {
    path: 'evaluation/:id',
    component: Evaluation,
  }, {
    path: 'faqs',
    component: FAQ,
  }, {
    path: 'logout',
    component: FAQ,
  }]
}


export default routes