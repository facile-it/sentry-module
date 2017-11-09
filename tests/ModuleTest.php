<?php

namespace Facile\SentryModuleTest;

use Facile\SentryModule\Module;
use Facile\SentryModule\Options\ConfigurationInterface;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\HeadScript;

class ModuleTest extends TestCase
{
    public function testOnBootstrapWithDisableJS()
    {
        $event = $this->prophesize(MvcEvent::class);
        $application = $this->prophesize(Application::class);
        $container = $this->prophesize(ContainerInterface::class);
        $configuration = $this->prophesize(ConfigurationInterface::class);

        $event->getApplication()->willReturn($application->reveal());
        $application->getServiceManager()->willReturn($container->reveal());
        $container->get(ConfigurationInterface::class)->willReturn($configuration->reveal());
        $configuration->shouldInjectRavenJavascript()->shouldBeCalled()->willReturn(false);
        $container->get('ViewHelperManager')->shouldNotBeCalled();

        $module = new Module();
        $module->onBootstrap($event->reveal());
    }

    public function testOnBootstrapWithEnabledJS()
    {
        $expectedScript = <<<SCRIPT
Raven.config('http://uri/1', {"foo":"bar"}).install();
SCRIPT;

        $event = $this->prophesize(MvcEvent::class);
        $application = $this->prophesize(Application::class);
        $container = $this->prophesize(ContainerInterface::class);
        $configuration = $this->prophesize(ConfigurationInterface::class);
        $viewHelperManager = $this->prophesize(ContainerInterface::class);
        $headScriptHelper = $this->prophesize(HeadScript::class);

        $event->getApplication()->willReturn($application->reveal());
        $application->getServiceManager()->willReturn($container->reveal());
        $container->get(ConfigurationInterface::class)->willReturn($configuration->reveal());
        $configuration->shouldInjectRavenJavascript()->shouldBeCalled()->willReturn(true);
        $configuration->getRavenJavascriptUri()->shouldBeCalled()->willReturn('http://js-uri');
        $configuration->getRavenJavascriptDsn()->shouldBeCalled()->willReturn('http://uri/1');
        $configuration->getRavenJavascriptOptions()->shouldBeCalled()->willReturn(['foo' => 'bar']);

        $container->get('ViewHelperManager')->shouldBeCalled()->willReturn($viewHelperManager->reveal());
        $viewHelperManager->get('HeadScript')->shouldBeCalled()->willReturn($headScriptHelper->reveal());
        $headScriptHelper->appendFile('http://js-uri')->shouldBeCalled();
        $headScriptHelper->appendScript($expectedScript)->shouldBeCalled();

        $module = new Module();
        $module->onBootstrap($event->reveal());
    }

    public function testOnBootstrapWithEnabledJSAndEmptyScript()
    {
        $expectedScript = <<<SCRIPT
Raven.config('http://uri/1', {"foo":"bar"}).install();
SCRIPT;

        $event = $this->prophesize(MvcEvent::class);
        $application = $this->prophesize(Application::class);
        $container = $this->prophesize(ContainerInterface::class);
        $configuration = $this->prophesize(ConfigurationInterface::class);
        $viewHelperManager = $this->prophesize(ContainerInterface::class);
        $headScriptHelper = $this->prophesize(HeadScript::class);

        $event->getApplication()->willReturn($application->reveal());
        $application->getServiceManager()->willReturn($container->reveal());
        $container->get(ConfigurationInterface::class)->willReturn($configuration->reveal());
        $configuration->shouldInjectRavenJavascript()->shouldBeCalled()->willReturn(true);
        $configuration->getRavenJavascriptUri()->shouldBeCalled()->willReturn('');
        $configuration->getRavenJavascriptDsn()->shouldBeCalled()->willReturn('http://uri/1');
        $configuration->getRavenJavascriptOptions()->shouldBeCalled()->willReturn(['foo' => 'bar']);

        $container->get('ViewHelperManager')->shouldBeCalled()->willReturn($viewHelperManager->reveal());
        $viewHelperManager->get('HeadScript')->shouldBeCalled()->willReturn($headScriptHelper->reveal());
        $headScriptHelper->appendFile(Argument::any())->shouldNotBeCalled();
        $headScriptHelper->appendScript($expectedScript)->shouldBeCalled();

        $module = new Module();
        $module->onBootstrap($event->reveal());
    }
}
