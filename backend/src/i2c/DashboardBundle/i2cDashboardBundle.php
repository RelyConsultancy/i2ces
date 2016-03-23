<?php

namespace i2c\DashboardBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class i2cDashboardBundle extends Bundle
{
    public function getParent()
    {
        return "OroDashboardBundle";
    }
}
