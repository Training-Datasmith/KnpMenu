<?php

declare (strict_types=1);
namespace Knp\Menu\Provider;

use Knp\Menu\Item_Interface;
use Psr\Container\Container_Interface;
/**
 * A menu provider getting the menus from a PSR-11 container.
 *
 * This menu provider does not support using options, as it cannot pass them to the container
 * to alter the menu building. Use a different provider in case you need support for options.
 *
 * @final since 3.8.0
 */
class Psr_Provider implements Menu_Provider_Interface
{
    public function __construct(private readonly Container_Interface $container)
    {
    }
    public function get(string $name, array $options = []): Item_Interface
    {
        if (!$this->container->has($name)) {
            throw new \InvalidArgumentException(\sprintf('The menu "%s" is not defined.', $name));
        }
        return $this->container->get($name);
    }
    public function has(string $name, array $options = []): bool
    {
        return $this->container->has($name);
    }
}