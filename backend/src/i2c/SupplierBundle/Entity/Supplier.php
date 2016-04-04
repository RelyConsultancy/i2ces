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
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;

/**
 * BusinessUnit
 *
 * @ORM\Table("oro_business_unit")
 * @ORM\Entity(repositoryClass="Oro\Bundle\OrganizationBundle\Entity\Repository\BusinessUnitRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Oro\Loggable
 * @Config(
 *      routeName="oro_business_unit_index",
 *      routeView="oro_business_unit_view",
 *      routeCreate="oro_business_unit_create",
 *      defaultValues={
 *          "grouping"={
 *              "groups"={"dictionary"}
 *          },
 *          "dictionary"={
 *              "search_fields"={"name"},
 *              "virtual_fields"={"id"},
 *              "activity_support"="true"
 *          },
 *          "entity"={
 *              "icon"="icon-building"
 *          },
 *          "ownership"={
 *              "owner_type"="BUSINESS_UNIT",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="business_unit_owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *          },
 *          "grid"={
 *              "default"="business-unit-grid"
 *          }
 *      }
 * )
 */
class Supplier extends BusinessUnit
{

    /**
     * @var string
     *
     * @JMS\Exclude()
     */
    protected $supplierLogo;
}
