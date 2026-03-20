<?php

declare (strict_types=1);
namespace Knp\Menu\Renderer;

interface Renderer_Provider_Interface
{
    /**
     * Retrieves a renderer by its name
     *
     * If null is given, a renderer marked as default is returned.
     *
     * @throws \InvalidArgumentException if the renderer does not exist
     */
    public function get(?string $name = null): Renderer_Interface;
    /**
     * Checks whether a renderer exists
     */
    public function has(string $name): bool;
}