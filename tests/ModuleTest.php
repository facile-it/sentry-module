<?php

declare(strict_types=1);

namespace Facile\SentryModuleTest;

use Facile\SentryModule\Module;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Helper\HeadScript;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Sentry\State\HubInterface;

class ModuleTest extends TestCase
{
    use ProphecyTrait;

    public function testGetConfig(): void
    {
        $module = new Module();

        $this->assertIsArray($module->getConfig());
        $this->assertArrayHasKey('service_manager', $module->getConfig());
        $this->assertArrayNotHasKey('dependencies', $module->getConfig());
    }

    public function testOnBootstrapWithDisableModule(): void
    {
        $event = $this->prophesize(MvcEvent::class);
        $application = $this->prophesize(Application::class);
        $container = $this->prophesize(ContainerInterface::class);

        $event->getApplication()->willReturn($application->reveal());
        $application->getServiceManager()->willReturn($container->reveal());
        $container->get(HubInterface::class)->shouldNotBeCalled();
        $container->get('ViewHelperManager')->shouldNotBeCalled();
        $container->get('HeadScript')->shouldNotBeCalled();
        $container->get('config')->willReturn([
            'sentry' => [
                'disable_module' => true,
                'options' => [],
                'javascript' => [
                    'inject_script' => false,
                ],
            ],
        ]);

        $module = new Module();
        $module->onBootstrap($event->reveal());
    }

    public function testOnBootstrapWithDisableJS(): void
    {
        $event = $this->prophesize(MvcEvent::class);
        $application = $this->prophesize(Application::class);
        $container = $this->prophesize(ContainerInterface::class);
        $hub = $this->prophesize(HubInterface::class);

        $event->getApplication()->willReturn($application->reveal());
        $application->getServiceManager()->willReturn($container->reveal());
        $container->get(HubInterface::class)->willReturn($hub->reveal());
        $container->get('ViewHelperManager')->shouldNotBeCalled();
        $container->get('HeadScript')->shouldNotBeCalled();
        $container->get('config')->willReturn([
            'sentry' => [
                'disable_module' => false,
                'options' => [],
                'javascript' => [
                    'inject_script' => false,
                ],
            ],
        ]);

        $module = new Module();
        $module->onBootstrap($event->reveal());
    }

    public function testOnBootstrapWithEnabledJS(): void
    {
        $expectedScript = <<<SCRIPT
            Sentry.init({"dsn":"http:\/\/uri\/1","foo":"bar"});
            SCRIPT;

        $event = $this->prophesize(MvcEvent::class);
        $application = $this->prophesize(Application::class);
        $container = $this->prophesize(ContainerInterface::class);
        $hub = $this->prophesize(HubInterface::class);
        $viewHelperManager = $this->prophesize(ContainerInterface::class);
        $headScriptHelper = $this->prophesize(HeadScript::class);

        $event->getApplication()->willReturn($application->reveal());
        $application->getServiceManager()->willReturn($container->reveal());
        $container->get(HubInterface::class)->willReturn($hub->reveal());
        $container->get('config')->willReturn([
            'sentry' => [
                'options' => [],
                'javascript' => [
                    'inject_script' => true,
                    'script' => [
                        'src' => 'http://js-uri',
                        'foo' => 'bar',
                    ],
                    'options' => [
                        'dsn' => 'http://uri/1',
                        'foo' => 'bar',
                    ],
                ],
            ],
        ]);

        $container->get('ViewHelperManager')->shouldBeCalled()->willReturn($viewHelperManager->reveal());
        $viewHelperManager->get('HeadScript')->shouldBeCalled()->willReturn($headScriptHelper->reveal());
        $headScriptHelper->appendFile('http://js-uri', 'text/javascript', ['src' => 'http://js-uri', 'foo' => 'bar'])->shouldBeCalled();
        $headScriptHelper->appendScript($expectedScript)->shouldBeCalled();

        $module = new Module();
        $module->onBootstrap($event->reveal());
    }
}
