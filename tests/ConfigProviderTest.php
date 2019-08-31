<?php

declare(strict_types=1);

namespace Facile\SentryModuleTest;

use Facile\SentryModule\ConfigProvider;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testInvoke(): void
    {
        $provider = new ConfigProvider();

        $this->assertIsCallable($provider);
        $this->assertIsArray($provider());
        $this->assertSame($provider->getDependencies(), $provider()['dependencies']);
    }

    public function testGetDependencies(): void
    {
        $provider = new ConfigProvider();

        $this->assertIsArray($provider->getDependencies());
    }
}
