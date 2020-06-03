<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bundle\ResourceBundle\Tests\Fixtures\Converter;

use Klipper\Component\Resource\Converter\ConverterInterface;

/**
 * Custom converter mock.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class CustomConverter implements ConverterInterface
{
    protected string $name = '';

    public function __construct($name = '')
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function convert(string $content): array
    {
        return [];
    }
}
