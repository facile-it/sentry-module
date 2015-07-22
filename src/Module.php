<?php
/**
 * Sentry Module
 *
 * @link      http://github.com/facile-it/sentry-module for the canonical source repository
 * @copyright Copyright (c) 2015 Facile.it
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License Version 2.0
 */

namespace Facile\SentryModule;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Class Module
 */
class Module implements ConfigProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
