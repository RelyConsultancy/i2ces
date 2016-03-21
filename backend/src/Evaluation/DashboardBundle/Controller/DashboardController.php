<?php

namespace Evaluation\DashboardBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use Oro\Bundle\DashboardBundle\Entity\Dashboard;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/dashboard")
 */
class DashboardController extends \Oro\Bundle\DashboardBundle\Controller\DashboardController
{
    /**
     * @param Dashboard $dashboard
     *
     * @Route(
     *      "/view/{id}",
     *      name="oro_dashboard_view",
     *      requirements={"id"="\d+"},
     *      defaults={"id" = "0","_format" = "html"}
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Dashboard $dashboard = null)
    {
        return new Response(
            $this->renderView('EvaluationDashboardBundle:Index:default.html.twig')
        );
    }
}
