<?php

declare(strict_types=1);

namespace Facile\SentryModule;

use Sentry\State\HubInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\HeadScript;
use Zend\View\HelperPluginManager;

final class Module
{
    public function getConfig(): array
    {
        $provider = new ConfigProvider();
        $config = $provider();
        $config['service_manager'] = $provider->getDependencies();
        unset($config['dependencies']);

        return $config;
    }

    public function onBootstrap(MvcEvent $e): void
    {
        $application = $e->getApplication();
        $container = $application->getServiceManager();

        // Get the Hub to initialize it
        $container->get(HubInterface::class);

        $config = $container->get('config')['sentry']['javascript'] ?? [];
        $options = $config['options'] ?? [];

        if (! ($config['inject_script'] ?? false)) {
            return;
        }

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        /** @var HeadScript $headScriptHelper */
        $headScriptHelper = $viewHelperManager->get('HeadScript');
        if ($config['script']['src'] ?? null) {
            $headScriptHelper->appendFile($config['script']['src'], 'text/javascript', $config['script']);
        }

        $headScriptHelper->appendScript(
            sprintf(
                'Sentry.init(%s);',
                \json_encode($options)
            )
        );
    }
}
