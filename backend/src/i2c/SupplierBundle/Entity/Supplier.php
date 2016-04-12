<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 04/04/16
 * Time: 12:30
 */

namespace i2c\SupplierBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\DataAuditBundle\Metadata\Annotation as Oro;

/**
 * Class Supplier
 *
 * @package i2c\SupplierBundle\Entity
 */
class Supplier extends BusinessUnit
{

    /**
     * @var string
     *
     * @JMS\Exclude()
     */
    protected $supplierLogo;

    /**
     * @var bool
     *
     * @JMS\Exclude()
     */
    protected $isNewSupplier = true;
}
