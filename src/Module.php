<?php

declare(strict_types=1);

namespace Facile\SentryModule;

use Laminas\Mvc\MvcEvent;
use Laminas\View\Helper\HeadScript;
use Laminas\View\HelperPluginManager;
use Sentry\State\HubInterface;

final class Module
{
    /**
     * @return array<string, mixed>
     */
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

        /** @var array<string, mixed> $appConfig */
        $appConfig = $container->get('config');

        if (($appConfig['sentry']['disable_module'] ?? false)) {
            return;
        }

        // Get the Hub to initialize it
        $container->get(HubInterface::class);

        $config = $appConfig['sentry']['javascript'] ?? [];
        $options = $config['options'] ?? [];

        if (! ($config['inject_script'] ?? false)) {
            return;
        }

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        /** @var HeadScript<mixed> $headScriptHelper */
        $headScriptHelper = $viewHelperManager->get('HeadScript');
        if ($config['script']['src'] ?? null) {
            $headScriptHelper->appendFile($config['script']['src'], 'text/javascript', $config['script']);
        }

        $headScriptHelper->appendScript(
            \sprintf(
                'Sentry.init(%s);',
                \json_encode($options)
            )
        );
    }
}
