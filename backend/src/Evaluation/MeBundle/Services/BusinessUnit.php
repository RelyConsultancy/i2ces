<?php

namespace Evaluation\MeBundle\Services;

use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit as BusinessUnitEntity;
use Oro\Bundle\UserBundle\Entity\User;

class BusinessUnit
{
    /**
     * Formats the user's business units into an array for frontend
     *
     * @param User $user
     *
     * @return array
     */
    public function getBusinessUnitsForUserAsArray(User $user)
    {
        $result = [];

        /** @var BusinessUnitEntity $businessUnit */
        foreach ($user->getBusinessUnits() as $businessUnit) {
            $result[] = [
                'id'      => $businessUnit->getId(),
                'name'    => $businessUnit->getName(),
                'email'   => $businessUnit->getEmail(),
                'phone'   => $businessUnit->getPhone(),
                'website' => $businessUnit->getWebsite(),
            ];
        }

        return $result;
    }
}
