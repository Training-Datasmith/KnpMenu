<?php

declare (strict_types=1);
namespace Knp\Menu\Twig;

use Knp\Menu\Item_Interface;
use Knp\Menu\Matcher\Matcher_Interface;
use Knp\Menu\Util\Menu_Manipulator;
use Twig\Extension\Runtime_Extension_Interface;
/**
 * @final since 3.8.0
 */
class Menu_Runtime_Extension implements Runtime_Extension_Interface
{
    public function __construct(private readonly Helper $helper, private readonly ?Matcher_Interface $matcher = null, private readonly ?Menu_Manipulator $menu_manipulator = null)
    {
    }
    /**
     * Retrieves an item following a path in the tree.
     *
     * @param array<int, string>   $path
     * @param array<string, mixed> $options
     *
     * @internal since 3.8.0
     */
    public function get(Item_Interface|string $menu, array $path = [], array $options = []): Item_Interface
    {
        return $this->helper->get($menu, $path, $options);
    }
    /**
     * Renders a menu with the specified renderer.
     *
     * @param string|ItemInterface|array<ItemInterface|string> $menu
     * @param array<string, mixed>                             $options
     *
     * @internal since 3.8.0
     */
    public function render(array|Item_Interface|string $menu, array $options = [], ?string $renderer = null): string
    {
        return $this->helper->render($menu, $options, $renderer);
    }
    /**
     * Returns an array ready to be used for breadcrumbs.
     *
     * @param string|ItemInterface|array<ItemInterface|string> $menu
     *
     * @phpstan-param string|array<int|string, string|int|float|null|array{label: string, uri: string|null, item: ItemInterface|null}|ItemInterface> $subItem
     *
     * @return array<int, array<string, mixed>>
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     *
     * @internal since 3.8.0
     */
    public function get_breadcrumbs_array(array|Item_Interface|string $menu, array|string|null $sub_item = null): array
    {
        return $this->helper->get_breadcrumbs_array($menu, $sub_item);
    }
    /**
     * Returns the current item of a menu.
     *
     * @internal since 3.8.0
     */
    public function get_current_item(Item_Interface|string $menu): Item_Interface
    {
        $root_item = $this->get($menu);
        $current_item = $this->helper->get_current_item($root_item);
        if (null === $current_item) {
            return $root_item;
        }
        return $current_item;
    }
    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     *
     * @internal since 3.8.0
     */
    public function path_as_string(Item_Interface $menu, string $separator = ' > '): string
    {
        if (null === $this->menu_manipulator) {
            throw new \BadMethodCallException('The menu manipulator must be set to get the breadcrumbs array');
        }
        return $this->menu_manipulator->get_path_as_string($menu, $separator);
    }
    /**
     * Checks whether an item is current.
     *
     * @internal since 3.8.0
     */
    public function is_current(Item_Interface $item): bool
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }
        return $this->matcher->is_current($item);
    }
    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param int|null $depth The max depth to look for the item
     *
     * @internal since 3.8.0
     */
    public function is_ancestor(Item_Interface $item, ?int $depth = null): bool
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }
        return $this->matcher->is_ancestor($item, $depth);
    }
}