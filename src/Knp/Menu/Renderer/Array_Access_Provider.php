<?php

declare (strict_types=1);
namespace Knp\Menu\Renderer;

/**
 * A renderer provider getting the renderers from a class implementing ArrayAccess.
 *
 * @final since 3.8.0
 */
class Array_Access_Provider implements Renderer_Provider_Interface
{
    /**
     * @param \ArrayAccess<string, RendererInterface> $registry
     * @param string                                  $defaultRenderer The name of the renderer used by default
     * @param array<string, string>                   $rendererIds     The map between renderer names and registry keys
     */
    public function __construct(private \ArrayAccess $registry, private readonly string $default_renderer, private array $renderer_ids)
    {
    }
    public function get(?string $name = null): Renderer_Interface
    {
        if (null === $name) {
            $name = $this->default_renderer;
        }
        if (!isset($this->renderer_ids[$name]) || null === $this->registry[$this->renderer_ids[$name]]) {
            throw new \InvalidArgumentException(\sprintf('The renderer "%s" is not defined.', $name));
        }
        return $this->registry[$this->renderer_ids[$name]];
    }
    public function has(string $name): bool
    {
        return isset($this->renderer_ids[$name]);
    }
}