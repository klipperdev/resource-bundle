<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\ResourceBundle\Tests\DependencyInjection\Compiler;

use Klipper\Bundle\ResourceBundle\DependencyInjection\Compiler\ConverterPass;
use Klipper\Bundle\ResourceBundle\Tests\Fixtures\Converter\CustomConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Tests case for converter pass compiler.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class ConverterPassTest extends TestCase
{
    protected ?string $rootDir = null;

    protected ?Filesystem $fs = null;

    protected ?ConverterPass $pass = null;

    protected function setUp(): void
    {
        $this->rootDir = sys_get_temp_dir().'/klipper_resource_bundle_converter_test';
        $this->fs = new Filesystem();
        $this->pass = new ConverterPass();
    }

    protected function tearDown(): void
    {
        $this->fs->remove($this->rootDir);
        $this->pass = null;
    }

    public function testProcessWithoutService(): void
    {
        $container = $this->getContainer();

        static::assertFalse($container->has('klipper_resource.converter_registry'));
        $this->pass->process($container);
        static::assertFalse($container->has('klipper_resource.converter_registry'));
    }

    public function testProcess(): void
    {
        $container = $this->getContainer([
            'KlipperResourceBundle' => 'Klipper\\Bundle\\ResourceBundle\\KlipperResourceBundle',
        ]);

        static::assertTrue($container->has('klipper_resource.converter_registry'));
        static::assertTrue($container->has('klipper_resource.converter.json'));

        $def = $container->getDefinition('klipper_resource.converter_registry');

        static::assertCount(1, $def->getArguments());
        static::assertEmpty($def->getArgument(0));

        $this->pass->process($container);

        static::assertCount(1, $def->getArguments());
        $arg = $def->getArgument(0);
        static::assertNotEmpty($arg);
        static::assertInstanceOf('Symfony\Component\DependencyInjection\Definition', $arg[0]);
    }

    public function testProcessWithInvalidInterface(): void
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);
        $this->expectExceptionMessage('The service id "test_invalid_converter_type" must be an class implementing the "Klipper\\Component\\Resource\\Converter\\ConverterInterface" interface.');

        $container = $this->getContainer([
            'KlipperResourceBundle' => 'Klipper\\Bundle\\ResourceBundle\\KlipperResourceBundle',
        ]);

        static::assertTrue($container->has('klipper_resource.converter_registry'));

        $def = new Definition('stdClass');
        $def->addTag('klipper_resource.converter');
        $container->setDefinition('test_invalid_converter_type', $def);

        $this->pass->process($container);
    }

    public function testProcessWithInvalidType(): void
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);
        $this->expectExceptionMessage('The service id "test_invalid_converter_type" must have the "type" parameter in the "klipper_resource.converter" tag.');

        $container = $this->getContainer([
            'KlipperResourceBundle' => 'Klipper\\Bundle\\ResourceBundle\\KlipperResourceBundle',
        ]);

        static::assertTrue($container->has('klipper_resource.converter_registry'));

        $def = new Definition(CustomConverter::class);
        $def->addTag('klipper_resource.converter');
        $container->setDefinition('test_invalid_converter_type', $def);

        $this->pass->process($container);
    }

    /**
     * Gets the container.
     */
    protected function getContainer(array $bundles = []): ContainerBuilder
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.cache_dir' => $this->rootDir.'/cache',
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => $this->rootDir,
            'kernel.charset' => 'UTF-8',
            'kernel.bundles' => $bundles,
        ]));

        if (\count($bundles) > 0) {
            $crDef = new Definition('Klipper\Component\Resource\Converter\ConverterRegistry');
            $crDef->addArgument([]);
            $container->setDefinition('klipper_resource.converter_registry', $crDef);

            $jcDef = new Definition('Klipper\Component\Resource\Converter\JsonConverter');
            $jcDef->addTag('klipper_resource.converter');
            $container->setDefinition('klipper_resource.converter.json', $jcDef);
        }

        return $container;
    }
}
