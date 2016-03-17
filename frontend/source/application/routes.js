import Dashboard from '/components/Dashboard'
import EvaluationList from '/components/EvaluationList'
import EvaluationDashboard from '/components/EvaluationDashboard'
import EvaluationChapters from '/components/EvaluationChapters'
import FAQ from '/components/FAQ'


const routes = {
  path: '/',
  component: Dashboard,
  indexRoute: {
    component: EvaluationList,
  },
  childRoutes: [{
    path: 'evaluations',
    component: EvaluationList,
  }, {
    path: 'evaluations/:cid',
    component: EvaluationDashboard,
  }, {
    path: 'evaluations/:cid/chapters/:id',
    component: EvaluationChapters,
  }, {
    path: 'faqs',
    component: FAQ,
  }, {
    path: 'logout',
    component: FAQ,
  }]
}


export default routes