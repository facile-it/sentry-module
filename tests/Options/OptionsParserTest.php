<?php

namespace Facile\SentryModule\Options;
use Facile\SentryModule\Options\RavenClient as RavenClientOptions;

/**
 * Class OptionsParserTest
 */
class OptionsParserTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateOptionsParser()
    {
        $config = [];
        $parser = new OptionsParser($config, 'dummy', 'default', 'My\Options\Dummy');
        $this->assertInstanceOf('Facile\SentryModule\Options\OptionsParser', $parser);
    }

    public function testGetOptions()
    {
        $config = [
            'sentry' => [
                'raven' => [
                    'default' => [
                        'dsn' => 'http://2222226666dddd:11113333cccc@sentry.yourdomain.com/2',
                        'options' => [
                            'release' => 'test'
                        ]
                    ]
                ]
            ]
        ];
        $parser = new OptionsParser($config, 'raven', 'default', 'Facile\SentryModule\Options\RavenClient');

        /* @var $options RavenClientOptions */
        $options = $parser->getOptions();
        $this->assertInstanceOf('Facile\SentryModule\Options\RavenClient', $options);
        $this->assertEquals($config['sentry']['raven']['default']['dsn'], $options->getDsn());
        $this->assertEquals($config['sentry']['raven']['default']['options'], $options->getOptions());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetOptionsWithException()
    {
        $config = [
            'sentry' => [
                'raven' => [
                ]
            ]
        ];
        $parser = new OptionsParser($config, 'notexistingoption', 'default', 'Facile\SentryModule\Options\RavenClient');
        $parser->getOptions();
    }
}
