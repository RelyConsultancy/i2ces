import Dashboard from '/components/Dashboard'
import EvaluationList from '/components/EvaluationList'
import EvaluationDashboard from '/components/EvaluationDashboard'
import EvaluationChapter from '/components/EvaluationChapter'
import EvaluationPreview from '/components/EvaluationPreview'
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
    component: EvaluationChapter,
  }, {
    path: 'preview/:cid',
    component: EvaluationPreview,
  }, {
    path: 'faqs',
    component: FAQ,
  }]
}


export default routes