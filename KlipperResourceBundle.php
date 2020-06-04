<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\ResourceBundle;

use Klipper\Bundle\ResourceBundle\DependencyInjection\Compiler\ConverterPass;
use Klipper\Bundle\ResourceBundle\DependencyInjection\Compiler\DomainPass;
use Klipper\Bundle\ResourceBundle\DependencyInjection\Compiler\TranslatorPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class KlipperResourceBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new TranslatorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -64);
        $container->addCompilerPass(new ConverterPass());
        $container->addCompilerPass(new DomainPass());
    }
}
