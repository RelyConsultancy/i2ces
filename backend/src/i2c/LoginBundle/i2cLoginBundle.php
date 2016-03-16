<?php

namespace i2c\LoginBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class i2cLoginBundle
 *
 * @package i2c\LoginBundle
 */
class i2cLoginBundle extends Bundle
{
    public function getParent()
    {
        return "OroUserBundle";
    }
}
