<?php

declare (strict_types=1);
namespace Knp\Menu\Renderer;

use Psr\Container\Container_Interface;
/**
 * A renderer provider getting the renderer from a PSR-11 container.
 *
 * This menu provider does not support using options, as it cannot pass them to the container
 * to alter the menu building. Use a different provider in case you need support for options.
 *
 * @final since 3.8.0
 */
class Psr_Provider implements Renderer_Provider_Interface
{
    /**
     * @param string $defaultRenderer id of the default renderer (it should exist in the container to avoid weird failures)
     */
    public function __construct(private readonly Container_Interface $container, private readonly string $default_renderer)
    {
    }
    public function get(?string $name = null): Renderer_Interface
    {
        if (null === $name) {
            $name = $this->default_renderer;
        }
        if (!$this->container->has($name)) {
            throw new \InvalidArgumentException(\sprintf('The renderer "%s" is not defined.', $name));
        }
        return $this->container->get($name);
    }
    public function has(string $name): bool
    {
        return $this->container->has($name);
    }
}