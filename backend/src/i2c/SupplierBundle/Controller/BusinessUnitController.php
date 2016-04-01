<?php

namespace i2c\SupplierBundle\Controller;

use i2c\SupplierBundle\Services\SupplierLogo;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/business_unit")
 */
class BusinessUnitController extends \Oro\Bundle\OrganizationBundle\Controller\BusinessUnitController
{
    /**
     * Edit business_unit form
     *
     * @Route("/update/{id}", name="oro_business_unit_update", requirements={"id"="\d+"}, defaults={"id"=0})
     * @Template
     * @Acl(
     *      id="oro_business_unit_update",
     *      type="entity",
     *      class="OroOrganizationBundle:BusinessUnit",
     *      permission="EDIT"
     * )
     */
    public function updateAction(BusinessUnit $entity)
    {
        $response = $this->update($entity);

        $oroLogoFolderPath = $this->getParameter('oro_attachment_path');

        $supplierLogoFolderPath = sprintf(
            '%s/%s/%s',
            $this->getParameter('upload_image_path'),
            $this->getParameter('supplier_logo_upload_directory'),
            $entity->getId()
        );

        $this->getSupplierLogoService()->createLogoHardLink(
            $entity->getId(),
            $oroLogoFolderPath,
            $supplierLogoFolderPath
        );

        return $response;
    }

    /**
     * @return SupplierLogo
     */
    protected function getSupplierLogoService()
    {
        return $this->get('i2c_supplier.supplier_logo_service');
    }
}
