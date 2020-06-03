<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class DomainPass implements CompilerPassInterface
{
    private ?array $resolveTargets = null;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('klipper_resource.domain_factory')
                || !$container->hasDefinition('doctrine')) {
            return;
        }

        $container->getDefinition('klipper_resource.domain_factory')
            ->addMethodCall('addResolveTargets', [$this->getResolveTargets($container)])
        ;
    }

    /**
     * Get the resolve target classes.
     *
     * @param ContainerBuilder $container The container
     */
    private function getResolveTargets(ContainerBuilder $container): array
    {
        if (null === $this->resolveTargets) {
            $this->resolveTargets = [];

            if ($container->hasDefinition('doctrine.orm.listeners.resolve_target_entity')) {
                $def = $container->getDefinition('doctrine.orm.listeners.resolve_target_entity');

                foreach ($def->getMethodCalls() as $call) {
                    if ('addResolveTargetEntity' === $call[0]) {
                        $this->resolveTargets[$call[1][0]] = $call[1][1];
                    }
                }
            }
        }

        return $this->resolveTargets;
    }
}
