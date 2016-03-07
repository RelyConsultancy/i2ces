<?php

namespace Evaluation\UtilBundle\Helpers;

use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;

/**
 * Class BusinessUnitHelper
 *
 * @package Evaluation\UtilBundle\Helpers
 */
final class BusinessUnitHelper
{
    /**
     * Returns a business unit as an array formatted for frontend
     *
     * @param BusinessUnit $businessUnit
     *
     * @return array
     */
    public static function getBusinessUnitAsArray(BusinessUnit $businessUnit)
    {
        return [
            'id' => $businessUnit->getId(),
            'name' => $businessUnit->getName(),
            'email' => $businessUnit->getEmail(),
            'phone' => $businessUnit->getPhone(),
            'website' => $businessUnit->getWebsite(),
        ];
    }

    /**
     * @param array|mixed $businessUnits Must be traversable with foreach
     *
     * @return array
     */
    public static function getBusinessUnitCollectionAsArray($businessUnits)
    {
        $result = [];

        /** @var BusinessUnit $businessUnit */
        foreach ($businessUnits as $businessUnit) {
            $result[] = static::getBusinessUnitAsArray($businessUnit);
        }

        return $result;
    }
}
