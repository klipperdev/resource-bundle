<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\ResourceBundle\Tests\DependencyInjection;

use Klipper\Bundle\ResourceBundle\DependencyInjection\KlipperResourceExtension;
use Klipper\Bundle\ResourceBundle\KlipperResourceBundle;
use Klipper\Component\Resource\Object\DefaultValueObjectFactory;
use Klipper\Component\Resource\Object\DoctrineObjectFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Tests case for Extension.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class KlipperResourceExtensionTest extends TestCase
{
    public function testExtensionExist(): void
    {
        $container = $this->createContainer();

        static::assertTrue($container->hasExtension('klipper_resource'));
    }

    public function testExtensionLoader(): void
    {
        $container = $this->createContainer();

        static::assertTrue($container->hasDefinition('klipper_resource.converter_registry'));
        static::assertTrue($container->hasDefinition('klipper_resource.domain_manager'));
        static::assertTrue($container->hasDefinition('klipper_resource.form_handler'));

        $def = $container->getDefinition('klipper_resource.object_factory');
        static::assertSame(DefaultValueObjectFactory::class, $def->getClass());
    }

    public function testExtensionDisableDefaultValue(): void
    {
        $container = $this->createContainer([
            [
                'object_factory' => [
                    'use_default_value' => false,
                ],
            ],
        ]);

        static::assertTrue($container->hasDefinition('klipper_resource.converter_registry'));
        static::assertTrue($container->hasDefinition('klipper_resource.domain_manager'));
        static::assertTrue($container->hasDefinition('klipper_resource.form_handler'));
        static::assertTrue($container->hasDefinition('klipper_resource.object_factory'));

        $def = $container->getDefinition('klipper_resource.object_factory');
        static::assertSame(DoctrineObjectFactory::class, $def->getClass());
    }

    protected function createContainer(array $configs = [])
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.bundles' => [
                'FrameworkBundle' => 'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle',
                'KlipperResourceBundle' => 'Klipper\\Bundle\\ResourceBundle\\KlipperResourceBundle',
            ],
            'kernel.bundles_metadata' => [],
            'kernel.cache_dir' => sys_get_temp_dir().'/klipper_resource_bundle',
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => sys_get_temp_dir().'/klipper_resource_bundle',
            'kernel.project_dir' => sys_get_temp_dir().'/klipper_resource_bundle',
            'kernel.charset' => 'UTF-8',
            'kernel.container_class' => 'TestContainer',
        ]));

        $sfExt = new FrameworkExtension();
        $extension = new KlipperResourceExtension();

        $container->registerExtension($sfExt);
        $container->registerExtension($extension);

        $sfExt->load([
            [
                'messenger' => [
                    'reset_on_message' => true,
                ],
                'form' => true,
            ],
        ], $container);
        $extension->load($configs, $container);

        $bundle = new KlipperResourceBundle();
        $bundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
