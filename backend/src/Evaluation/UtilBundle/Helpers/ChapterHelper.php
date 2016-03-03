<?php

namespace Evaluation\UtilBundle\Helpers;

/**
 * Class ChapterHelper
 *
 * @package Evaluation\UtilBundle\Helpers
 */
final class ChapterHelper
{
    const STATE_VISIBLE = 'visible';
    const STATE_HIDDEN = 'hidden';

    public static $states = [
        self::STATE_HIDDEN => self::STATE_HIDDEN,
        self::STATE_VISIBLE => self::STATE_VISIBLE,
    ];
}
