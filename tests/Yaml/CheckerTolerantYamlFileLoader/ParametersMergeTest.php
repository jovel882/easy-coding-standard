<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml\CheckerTolerantYamlFileLoader;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;

final class ParametersMergeTest extends TestCase
{
    /**
     * @dataProvider provideConfigToParameters()
     * @param mixed[] $expectedParameters
     */
    public function test(string $configFile, array $expectedParameters, string $message): void
    {
        $containerBuilder = $this->createAndLoadContainerBuilderFromConfig($configFile);

        $this->assertSame($expectedParameters, $containerBuilder->getParameterBag()->all(), $message);
    }

    public function provideConfigToParameters(): Iterator
    {
        yield [
            __DIR__ . '/ParametersSource/config-skip-with-import.yml',
            [
                'skip' => [
                    'secondCode' => false,
                    'thirdCode' => null,
                    'firstCode' => null,
                ],
            ],
            'import parent with already defined parameters with same keys',
        ];

        yield [
            __DIR__ . '/ParametersSource/config-skip-with-import-empty.yml',
            [
                'skip' => [
                    'firstCode' => null,
                    'secondCode' => null,
                ],
            ],
            'import empty config',
        ];

        yield [
            __DIR__ . '/ParametersSource/config-string-overide.yml',
            [
                'key' => 'new_value',
            ],
            'override string key',
        ];
    }

//    /**
//     * Covers bit complicated issue https://github.com/Symplify/Symplify/issues/736
//     */
//    public function testMainConfigValueOverride(): void
//    {
//        $containerBuilder = new ContainerBuilder();
//
//        $yamlFileLoader = new CheckerTolerantYamlFileLoader($containerBuilder, new FileLocator(__DIR__));
//        // local "src/config/config.yml"
//        $yamlFileLoader->load(__DIR__ . '/../../../src/config/config.yml');
//        // mimics user's "easy-config-standard.yml" with own values
//        $yamlFileLoader->load(__DIR__ . '/ParametersSource/root-config-override.yml');
//
//        $this->assertSame([
//            'cache_directory' => 'new_value',
//        ], $containerBuilder->getParameterBag()->all());
//    }

    private function createAndLoadContainerBuilderFromConfig(string $config): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $delegatingLoader = (new DelegatingLoaderFactory())->createContainerBuilderAndConfig($containerBuilder, $config);
        $delegatingLoader->load($config);

        return $containerBuilder;
    }
}
