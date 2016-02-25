<?php

namespace Evaluation\DashboardBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EvaluationDashboardBundle extends Bundle
{
    public function getParent()
    {
        return "OroDashboardBundle";
    }
}
