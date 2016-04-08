<?php

namespace i2c\SupplierBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class i2cSupplierBundle
 *
 * @package i2c\SupplierBundle
 */
class i2cSupplierBundle extends Bundle
{
    /**
     * @return string
     */
    public function getParent()
    {
        return "OroOrganizationBundle";
    }
}
