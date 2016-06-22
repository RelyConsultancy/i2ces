<?php

namespace i2c\DashboardBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use Oro\Bundle\DashboardBundle\Entity\Dashboard;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
            $this->renderView('i2cDashboardBundle:Index:default.html.twig')
        );
    }

    /**
     * @Route(
     *      ".{_format}",
     *      name="oro_dashboard_index",
     *      requirements={"_format"="html|json"},
     *      defaults={"_format" = "html"}
     * )
     *
     * @Acl(
     *      id="oro_dashboard_view",
     *      type="entity",
     *      class="OroDashboardBundle:Dashboard",
     *      permission="VIEW"
     * )
     * @Template
     */
    public function indexAction()
    {
        $isEmployee = $this->get('oro_security.security_facade')
                           ->isGranted('EDIT', 'Entity:i2cEvaluationBundle:Evaluation');
        if (!$isEmployee) {
            return $this->render(
                'TwigBundle:Exception:error.html.twig',
                array('status_code' => 403, 'status_text' => 'Forbidden')
            );
        }

        return [
            'entity_class' => $this->container->getParameter('oro_dashboard.dashboard_entity.class'),
        ];
    }
}
