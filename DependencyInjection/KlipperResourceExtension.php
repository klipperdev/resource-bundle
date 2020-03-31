<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\ResourceBundle\DependencyInjection;

use Klipper\Component\Resource\Object\DefaultValueObjectFactory;
use Klipper\Component\Resource\Object\DoctrineObjectFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class KlipperResourceExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('domain.xml');

        if (class_exists(Form::class)) {
            $loader->load('converter.xml');
            $loader->load('handler.xml');
            $container->setParameter('klipper_resource.form_handler_default_limit', $config['form_handler_default_limit']);
            $container->setParameter('klipper_resource.form_handler_max_limit', $config['form_handler_max_limit']);

            if (\function_exists('json_encode')) {
                $loader->load('converter_json.xml');

                if (class_exists(\SimpleXMLElement::class)) {
                    $loader->load('converter_xml.xml');
                }
            }
        }

        $container->setParameter('klipper_resource.domain.undelete_disable_filters', $config['undelete_disable_filters']);

        $container->setDefinition('klipper_resource.object_factory', $this->getObjectFactoryDefinition($config));
    }

    /**
     * Get the object factory definition.
     *
     * @param array $config The config
     *
     * @return Definition
     */
    private function getObjectFactoryDefinition(array $config): Definition
    {
        if ($config['object_factory']['use_default_value']) {
            $class = DefaultValueObjectFactory::class;
            $args = [new Reference('klipper_default_value.factory')];
        } else {
            $class = DoctrineObjectFactory::class;
            $args = [new Reference('doctrine.orm.entity_manager')];
        }

        return (new Definition($class, $args))->setPublic(true);
    }
}
