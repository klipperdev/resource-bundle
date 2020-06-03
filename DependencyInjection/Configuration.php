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

use Klipper\Bundle\DefaultValueBundle\KlipperDefaultValueBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('klipper_resource');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->integerNode('form_handler_default_limit')->defaultNull()->end()
            ->integerNode('form_handler_max_limit')->defaultValue(200)->end()
            ->arrayNode('undelete_disable_filters')
            ->defaultValue(['soft_deletable'])
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('object_factory')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('use_default_value')
            ->defaultValue(class_exists(KlipperDefaultValueBundle::class))
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
