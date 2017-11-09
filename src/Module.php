<?php

namespace Facile\SentryModule;

use Facile\SentryModule\Options\ConfigurationInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module.
 */
class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return include __DIR__.'/../config/module.config.php';
    }

    /**
     * Listen to the bootstrap event.
     *
     * @param EventInterface $e
     *
     * @return void
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Zend\ServiceManager\Exception\InvalidServiceException
     * @throws \RuntimeException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function onBootstrap(EventInterface $e)
    {
        /* @var MvcEvent $e */
        $application = $e->getApplication();
        $container = $application->getServiceManager();

        /** @var ConfigurationInterface $configuration */
        $configuration = $container->get(ConfigurationInterface::class);

        if ($configuration->shouldInjectRavenJavascript()) {
            /** @var \Zend\View\HelperPluginManager $viewHelperManager */
            $viewHelperManager = $container->get('ViewHelperManager');
            /** @var \Zend\View\Helper\HeadScript $headScriptHelper */
            $headScriptHelper = $viewHelperManager->get('HeadScript');
            $jsScript = $configuration->getRavenJavascriptUri();
            if (! empty($jsScript)) {
                $headScriptHelper->appendFile($jsScript);
            }
            $headScriptHelper->appendScript(
                sprintf(
                    'Raven.config(\'%s\', %s).install();',
                    $configuration->getRavenJavascriptDsn(),
                    json_encode($configuration->getRavenJavascriptOptions())
                )
            );
        }
    }
}
