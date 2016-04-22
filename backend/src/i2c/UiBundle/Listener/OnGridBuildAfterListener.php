<?php

namespace i2c\UiBundle\Listener;

use Oro\Bundle\DataGridBundle\Event\BuildAfter;

/**
 * Class OnGridBuildAfterListener
 *
 * @package i2c\UiBundle\Listener
 */
class OnGridBuildAfterListener
{
    /**
     * @param BuildAfter $event
     */
    public function onBuildAfter(BuildAfter $event)
    {
        $config = $event->getDatagrid()->getConfig();
        $enabledFilter = $config->offsetGetByPath('[filters][columns][enabled]');

        $enabledFilter['options']['field_options']['choices'] = [
            0 => 'Inactive',
            1 => 'Active',
        ];

        $config->offsetUnsetByPath('[filters][columns][enabled]');
        $config->offsetAddToArrayByPath('[filters][columns][enabled]', $enabledFilter);

        return;
    }
}
