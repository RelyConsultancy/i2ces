<?php

namespace i2c\MeBundle\Controller\Api;

use FOS\RestBundle\View\View;
use i2c\EvaluationBundle\Controller\Api\RestApiController;
use i2c\EvaluationBundle\Services\EvaluationDataBaseManager;
use i2c\ImageUploadBundle\Services\ImageProcessing;
use i2c\MeBundle\Services\BusinessUnit as BusinessUnitService;
use i2c\SupplierBundle\Services\SupplierLogo;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

        $logo['label'] = $businessUnit->getName();

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
        try {
            $logo['path'] = sprintf(
                '/%s/%s/%s/%s',
                'images',
                $this->getParameter('supplier_logo_upload_directory'),
                $businessUnitId,
                $logoName
            );

            $logoInfo = $this->getImageProcessingService()->getImageDetails(
                sprintf(
                    '%s/%s/%s/%s',
                    $this->getParameter('upload_image_path'),
                    $this->getParameter('supplier_logo_upload_directory'),
                    $businessUnitId,
                    $logoName
                )
            );

            $logo['width'] = $logoInfo->getWidth();
            $logo['height'] = $logoInfo->getHeight();

            return $logo;
        } catch (FileException $ex) {
            $this->get('logger')->addCritical($ex->getTraceAsString());
            return [];
        }
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

    /**
     * @return ImageProcessing
     */
    protected function getImageProcessingService()
    {
        return $this->get('i2c_image_upload.image_processing');
    }
}
