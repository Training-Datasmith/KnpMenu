<?php

declare (strict_types=1);
namespace Knp\Menu\Provider;

use Knp\Menu\Item_Interface;
/**
 * A menu provider getting the menus from a class implementing ArrayAccess.
 *
 * In case the value stored in the registry is a callable rather than an ItemInterface,
 * it will be called with the options as first argument and the registry as second argument
 * and is expected to return a menu item.
 *
 * @final since 3.8.0
 */
class Array_Access_Provider implements Menu_Provider_Interface
{
    /**
     * @param \ArrayAccess<string, ItemInterface|callable> $registry
     * @param array<string, string>                        $menuIds  The map between menu identifiers and registry keys
     */
    public function __construct(private \ArrayAccess $registry, private array $menu_ids = [])
    {
    }
    public function get(string $name, array $options = []): Item_Interface
    {
        if (!isset($this->menu_ids[$name])) {
            throw new \InvalidArgumentException(\sprintf('The menu "%s" is not defined.', $name));
        }
        $menu = $this->registry[$this->menu_ids[$name]];
        if (\is_callable($menu)) {
            return $menu($options, $this->registry);
        }
        return $menu;
    }
    public function has(string $name, array $options = []): bool
    {
        return isset($this->menu_ids[$name]);
    }
}