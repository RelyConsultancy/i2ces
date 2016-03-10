import Dashboard from '/component/Dashboard'
import EvaluationList from '/component/EvaluationList'
import EvaluationDashboard from '/component/EvaluationDashboard'
import EvaluationChapters from '/component/EvaluationChapters'
import FAQ from '/component/FAQ'


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