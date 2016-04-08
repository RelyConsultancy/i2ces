<?php

namespace i2c\MeBundle\Controller\Api;

use FOS\RestBundle\View\View;
use i2c\EvaluationBundle\Controller\Api\RestApiController;
use i2c\EvaluationBundle\Services\EvaluationDataBaseManager;
use i2c\MeBundle\Services\BusinessUnit as BusinessUnitService;
use i2c\SupplierBundle\Services\SupplierLogo;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package i2c\MeBundle\Controller\Api
 */
class UserController extends RestApiController
{
    /**
     * Returns a json with the current user data.
     *
     * @return View
     */
    public function getMeAction()
    {
        $isEmployee = $this->get('oro_security.security_facade')
                           ->isGranted('EDIT', 'Entity:i2cEvaluationBundle:Evaluation');

        /** @var User $user */
        $user = $this->getUser();

        if ($isEmployee) {
            $viewEvaluations = $this->getEvaluationDatabaseManagerService()->getAllForViewing();
        } else {
            $viewEvaluations = $this->getEvaluationDatabaseManagerService()->getAllPublishedForViewing();
        }

        $editEvaluations = $this->getEvaluationDatabaseManagerService()->getAllForEditing();

        /** @var BusinessUnit $businessUnit */
        $businessUnit = $user->getBusinessUnits()->first();

        $logoName = $this->getSupplierLogoService()->getSupplierLogoName($businessUnit->getId());

        if (false != $logoName) {
            $logo = $this->getLogoDetails($logoName, $businessUnit->getId());
        }

        $logo['label'] = $this->getSupplierLogoService()->getLogoLabel($businessUnit->getId());

        $data = array(
            'id'             => $user->getId(),
            'username'       => $user->getUsername(),
            'business_units' => $this->getBusinessUnitService()->getBusinessUnitsForUserAsArray($user),
            'view'           => $viewEvaluations,
            'edit'           => $editEvaluations,
            'host'           => $this->container->get('request')->getSchemeAndHttpHost(),
            'logo'           => $logo,
        );

        if ($isEmployee) {
            $data['type'] = 'i2c_employee';
        } else {
            $data['type'] = 'supplier';
        }

        return $this->success($data, Response::HTTP_OK, 'minimal');
    }

    /**
     * @param $logoName
     * @param $businessUnitId
     *
     * @return array
     */
    protected function getLogoDetails($logoName, $businessUnitId)
    {
        $logo['path'] = sprintf(
            '/%s/%s/%s/%s',
            'images',
            $this->getParameter('supplier_logo_upload_directory'),
            $businessUnitId,
            $logoName
        );

        $logoInfo = getimagesize(
            sprintf(
                '%s/%s/%s/%s',
                $this->getParameter('upload_image_path'),
                $this->getParameter('supplier_logo_upload_directory'),
                $businessUnitId,
                $logoName
            )
        );

        if (false == $logoInfo) {
            return [];
        }

        $logo['width'] = $logoInfo[0];
        $logo['height'] = $logoInfo[1];

        return $logo;
    }

    /**
     * @return EvaluationDataBaseManager
     */
    public function getEvaluationDatabaseManagerService()
    {
        return $this->get('i2c_evaluation.evaluation_database_manager_service');
    }

    /**
     * @return BusinessUnitService
     */
    public function getBusinessUnitService()
    {
        return $this->get('i2c_me.business_unit_service');
    }

    /**
     * @return SupplierLogo
     */
    public function getSupplierLogoService()
    {
        return $this->get('i2c_supplier.supplier_logo_service');
    }
}
