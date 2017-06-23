<?php

namespace Facile\SentryModuleTest\Processor;

use Facile\SentryModule\Processor\SanitizeDataProcessor;
use PHPUnit\Framework\TestCase;
use Raven_Client;

class SanitizeDataProcessorTest extends TestCase
{
    /**
     * @dataProvider sanitizeDataProvider
     * @param array $data
     * @param array $expected
     */
    public function testSanitize(array $data, array $expected)
    {
        $previousMock = $this->prophesize(\Raven_Processor::class);
        $ravenClientMock = $this->prophesize(Raven_Client::class);

        $previousMock->process($data)->shouldBeCalled();

        $processor = new SanitizeDataProcessor($ravenClientMock->reveal(), $previousMock->reveal());
        $processor->process($data);

        $this->assertSame($expected, $data);
    }

    public function sanitizeDataProvider()
    {
        return [
            [
                [
                    'stacktrace' => [
                        'frames' => [
                            [
                                'vars' => [
                                    'bool' => true,
                                    'string' => 'string',
                                    'int' => 5,
                                    'float' => 5.5,
                                    'object' => new SimpleObject(),
                                    'stringObject' => new ToStringObject(),
                                    'resource' => fopen('php://temp', 'rb+'),
                                    'null' => null,
                                    'callable' => function () {
                                    },
                                    'array' => [
                                        'recursive' => [
                                            'bool' => true,
                                            'string' => 'string',
                                            'int' => 5,
                                            'float' => 5.5,
                                            'object' => new SimpleObject(),
                                            'stringObject' => new ToStringObject(),
                                            'resource' => fopen('php://temp', 'rb+'),
                                            'null' => null,
                                            'callable' => function () {
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'stacktrace' => [
                        'frames' => [
                            [
                                'vars' => [
                                    'bool' => true,
                                    'string' => 'string',
                                    'int' => 5,
                                    'float' => 5.5,
                                    'object' => '[object Facile\SentryModuleTest\Processor\SimpleObject]',
                                    'stringObject' => '[Object: "this is an object"]',
                                    'resource' => '[resource]',
                                    'null' => null,
                                    'callable' => '[object Closure]',
                                    'array' => [
                                        'recursive' => [
                                            'bool' => true,
                                            'string' => 'string',
                                            'int' => 5,
                                            'float' => 5.5,
                                            'object' => '[object Facile\SentryModuleTest\Processor\SimpleObject]',
                                            'stringObject' => '[Object: "this is an object"]',
                                            'resource' => '[resource]',
                                            'null' => null,
                                            'callable' => '[object Closure]',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                [
                    'exception' => [
                        'values' => [
                            [
                                'frames' => [
                                    [
                                        'vars' => [
                                            'bool' => true,
                                            'string' => 'string',
                                            'int' => 5,
                                            'float' => 5.5,
                                            'object' => new SimpleObject(),
                                            'stringObject' => new ToStringObject(),
                                            'resource' => fopen('php://temp', 'rb+'),
                                            'null' => null,
                                            'callable' => function () {
                                            },
                                            'array' => [
                                                'recursive' => [
                                                    'bool' => true,
                                                    'string' => 'string',
                                                    'int' => 5,
                                                    'float' => 5.5,
                                                    'object' => new SimpleObject(),
                                                    'stringObject' => new ToStringObject(),
                                                    'resource' => fopen('php://temp', 'rb+'),
                                                    'null' => null,
                                                    'callable' => function () {
                                                    },
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'exception' => [
                        'values' => [
                            [
                                'frames' => [
                                    [
                                        'vars' => [
                                            'bool' => true,
                                            'string' => 'string',
                                            'int' => 5,
                                            'float' => 5.5,
                                            'object' => '[object Facile\SentryModuleTest\Processor\SimpleObject]',
                                            'stringObject' => '[Object: "this is an object"]',
                                            'resource' => '[resource]',
                                            'null' => null,
                                            'callable' => '[object Closure]',
                                            'array' => [
                                                'recursive' => [
                                                    'bool' => true,
                                                    'string' => 'string',
                                                    'int' => 5,
                                                    'float' => 5.5,
                                                    'object' => '[object Facile\SentryModuleTest\Processor\SimpleObject]',
                                                    'stringObject' => '[Object: "this is an object"]',
                                                    'resource' => '[resource]',
                                                    'null' => null,
                                                    'callable' => '[object Closure]',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                [
                    'extra' => [
                        'bool' => true,
                        'string' => 'string',
                        'int' => 5,
                        'float' => 5.5,
                        'object' => new SimpleObject(),
                        'stringObject' => new ToStringObject(),
                        'resource' => fopen('php://temp', 'rb+'),
                        'null' => null,
                        'callable' => function () {
                        },
                        'array' => [
                            'recursive' => [
                                'bool' => true,
                                'string' => 'string',
                                'int' => 5,
                                'float' => 5.5,
                                'object' => new SimpleObject(),
                                'stringObject' => new ToStringObject(),
                                'resource' => fopen('php://temp', 'rb+'),
                                'null' => null,
                                'callable' => function () {
                                },
                            ],
                        ],
                    ],
                ],
                [
                    'extra' => [
                        'bool' => true,
                        'string' => 'string',
                        'int' => 5,
                        'float' => 5.5,
                        'object' => '[object Facile\SentryModuleTest\Processor\SimpleObject]',
                        'stringObject' => '[Object: "this is an object"]',
                        'resource' => '[resource]',
                        'null' => null,
                        'callable' => '[object Closure]',
                        'array' => [
                            'recursive' => [
                                'bool' => true,
                                'string' => 'string',
                                'int' => 5,
                                'float' => 5.5,
                                'object' => '[object Facile\SentryModuleTest\Processor\SimpleObject]',
                                'stringObject' => '[Object: "this is an object"]',
                                'resource' => '[resource]',
                                'null' => null,
                                'callable' => '[object Closure]',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
