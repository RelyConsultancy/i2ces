<?php

namespace i2c\SupplierBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Authentication\Token\UsernamePasswordOrganizationToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class OrganizationController
 *
 * @package i2c\SupplierBundle\Controller
 */
class OrganizationController extends \Oro\Bundle\OrganizationBundle\Controller\OrganizationController
{
    /**
     * Checks the user permissions and denies access to the organization edit page for 'supplier' users
     *
     * Edit organization form
     *
     * @Route("/update_current", name="oro_organization_update_current")
     * @Template("OroOrganizationBundle:Organization:update.html.twig")
     * @Acl(
     *      id="oro_organization_update",
     *      type="entity",
     *      class="OroOrganizationBundle:Organization",
     *      permission="EDIT"
     * )
     */
    public function updateCurrentAction()
    {
        /** @var UsernamePasswordOrganizationToken $token */
        $token = $this->get('security.context')->getToken();
        $isEmployee = $this->get('oro_security.security_facade')
                           ->isGranted('EDIT', 'Entity:i2cEvaluationBundle:Evaluation');
        if (!$isEmployee) {
            return $this->render(
                'TwigBundle:Exception:error.html.twig',
                array('status_code' => 403, 'status_text' => 'Forbidden')
            );
        }
        $organization = $token->getOrganizationContext();

        return $this->update($organization);
    }
}
