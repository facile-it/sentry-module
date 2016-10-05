<?php

namespace Facile\SentryModuleTest;

use Facile\SentryModule\Module;
use Facile\SentryModule\Options\ConfigurationOptions;
use Facile\SentryModule\Service\Client;
use Facile\SentryModule\Service\ErrorHandlerRegister;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $module = new Module();
        $config = $module->getConfig();

        static::assertInternalType('array', $config);
        static::assertTrue(isset($config['facile']['sentry']['client']));
        static::assertTrue(isset($config['facile']['sentry_factories']['client']));
    }

    public function testOnBootstrap()
    {
        $module = new Module();

        $config = [
            'facile' => [
                'sentry' => [
                    'client' => [
                        'default' => [],
                    ],
                ],
            ],
        ];
        $event = $this->prophesize(MvcEvent::class);
        $application = $this->prophesize(Application::class);
        $eventManager = $this->prophesize(EventManagerInterface::class);
        $errorHandlerRegister = $this->prophesize(ErrorHandlerRegister::class);
        $clientDefault = $this->prophesize(Client::class);
        $container = $this->prophesize(ServiceManager::class);
        $configurationOptions = $this->prophesize(ConfigurationOptions::class);

        $configurationOptions->getRavenJavascriptDsn()->willReturn('foo-dsn');
        $configurationOptions->isInjectRavenJavascript()->willReturn(false);

        $event->getApplication()->willReturn($application->reveal());
        $application->getServiceManager()->willReturn($container->reveal());
        $application->getEventManager()->willReturn($eventManager->reveal());
        $container->get('config')->willReturn($config);
        $container->get(ErrorHandlerRegister::class)->willReturn($errorHandlerRegister->reveal());
        $container->get(ConfigurationOptions::class)->willReturn($configurationOptions->reveal());

        $container->get('facile.sentry.client.default')->willReturn($clientDefault->reveal());
        $errorHandlerRegister->registerHandlers($clientDefault->reveal(), $eventManager->reveal())
            ->shouldBeCalled();

        $module->onBootstrap($event->reveal());
    }

    public function testOnBootstrapWithJavascript()
    {
        $module = new Module();

        $config = [
            'facile' => [
                'sentry' => [
                    'client' => [
                        'default' => [],
                    ],
                ],
            ],
        ];
        $event = $this->prophesize(MvcEvent::class);
        $application = $this->prophesize(Application::class);
        $eventManager = $this->prophesize(EventManagerInterface::class);
        $errorHandlerRegister = $this->prophesize(ErrorHandlerRegister::class);
        $clientDefault = $this->prophesize(Client::class);
        $container = $this->prophesize(ServiceManager::class);
        $configurationOptions = $this->prophesize(ConfigurationOptions::class);
        $viewHelperManager = $this->prophesize(\Zend\View\HelperPluginManager::class);
        $headScriptHelper = $this->prophesize(\Zend\View\Helper\HeadScript::class);

        $viewHelperManager->get('HeadScript')->willReturn($headScriptHelper->reveal());

        $configurationOptions->getRavenJavascriptDsn()->willReturn('foo-dsn');
        $configurationOptions->getRavenJavascriptUri()->willReturn('foo-uri');
        $configurationOptions->getRavenJavascriptOptions()->willReturn(['foo' => 'bar']);
        $configurationOptions->isInjectRavenJavascript()->willReturn(true);

        $event->getApplication()->willReturn($application->reveal());
        $application->getServiceManager()->willReturn($container->reveal());
        $application->getEventManager()->willReturn($eventManager->reveal());
        $container->get('config')->willReturn($config);
        $container->get(ErrorHandlerRegister::class)->willReturn($errorHandlerRegister->reveal());
        $container->get(ConfigurationOptions::class)->willReturn($configurationOptions->reveal());
        $container->get('ViewHelperManager')->willReturn($viewHelperManager->reveal());

        $container->get('facile.sentry.client.default')->willReturn($clientDefault->reveal());
        $errorHandlerRegister->registerHandlers($clientDefault->reveal(), $eventManager->reveal());

        $headScriptHelper->appendFile('foo-uri')->shouldBeCalled();
        $headScriptHelper->appendScript('Raven.config(\'foo-dsn\', {"foo":"bar"}).install();')->shouldBeCalled();

        $module->onBootstrap($event->reveal());
    }
}
