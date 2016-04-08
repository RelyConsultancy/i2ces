<?php

namespace i2c\SupplierBundle\DependencyInjection;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration extends \Oro\Bundle\OrganizationBundle\DependencyInjection\Configuration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        return parent::getConfigTreeBuilder();
    }
}
