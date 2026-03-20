<?php

declare (strict_types=1);
namespace Knp\Menu\Twig;

use Knp\Menu\Item_Interface;
use Knp\Menu\Matcher\Matcher_Interface;
use Knp\Menu\Provider\Menu_Provider_Interface;
use Knp\Menu\Renderer\Renderer_Provider_Interface;
use Knp\Menu\Util\Menu_Manipulator;
/**
 * Helper class containing logic to retrieve and render menus from templating engines
 */
class Helper
{
    public function __construct(private readonly Renderer_Provider_Interface $renderer_provider, private readonly ?Menu_Provider_Interface $menu_provider = null, private readonly ?Menu_Manipulator $menu_manipulator = null, private readonly ?Matcher_Interface $matcher = null)
    {
    }
    /**
     * Retrieves item in the menu, eventually using the menu provider.
     *
     * @param ItemInterface|string $menu
     * @param array<int, string>   $path
     * @param array<string, mixed> $options
     *
     * @throws \BadMethodCallException   when there is no menu provider and the menu is given by name
     * @throws \LogicException
     * @throws \InvalidArgumentException when the path is invalid
     */
    public function get($menu, array $path = [], array $options = []): Item_Interface
    {
        if (!$menu instanceof Item_Interface) {
            if (null === $this->menu_provider) {
                throw new \BadMethodCallException('A menu provider must be set to retrieve a menu');
            }
            $menu_name = $menu;
            $menu = $this->menu_provider->get($menu_name, $options);
        }
        foreach ($path as $child) {
            $menu = $menu->get_child($child);
            if (null === $menu) {
                throw new \InvalidArgumentException(\sprintf('The menu has no child named "%s"', $child));
            }
        }
        return $menu;
    }
    /**
     * Renders a menu with the specified renderer.
     *
     * If the argument is an array, it will follow the path in the tree to
     * get the needed item. The first element of the array is the whole menu.
     * If the menu is a string instead of an ItemInterface, the provider
     * will be used.
     *
     * @param ItemInterface|string|array<ItemInterface|string> $menu
     * @param array<string, mixed>                             $options
     *
     * @throws \InvalidArgumentException
     */
    public function render($menu, array $options = [], ?string $renderer = null): string
    {
        $menu = $this->cast_menu($menu);
        return $this->renderer_provider->get($renderer)->render($menu, $options);
    }
    /**
     * Renders an array ready to be used for breadcrumbs.
     *
     * Each element in the array will be an array with 3 keys:
     * - `label` containing the label of the item
     * - `uri` containing the url of the item (may be `null`)
     * - `item` containing the original item (may be `null` for the extra items)
     *
     * The subItem can be one of the following forms
     *   * 'subItem'
     *   * ItemInterface object
     *   * ['subItem' => '@homepage']
     *   * ['subItem1', 'subItem2']
     *   * [['label' => 'subItem1', 'uri' => '@homepage'], ['label' => 'subItem2']]
     *
     * @param mixed $menu
     * @param mixed $subItem A string or array to append onto the end of the array
     *
     * @phpstan-param string|ItemInterface|array<int|string, string|int|float|null|array{label: string, uri: string|null, item: ItemInterface|null}|ItemInterface>|\Traversable<string|int|float|null|array{label: string, uri: string|null, item: ItemInterface|null}|ItemInterface> $subItem
     *
     * @return array<int, array<string, mixed>>
     *
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     */
    public function get_breadcrumbs_array($menu, $sub_item = null): array
    {
        if (null === $this->menu_manipulator) {
            throw new \BadMethodCallException('The menu manipulator must be set to get the breadcrumbs array');
        }
        $menu = $this->cast_menu($menu);
        return $this->menu_manipulator->get_breadcrumbs_array($menu, $sub_item);
    }
    /**
     * Returns the current item of a menu.
     *
     * @param ItemInterface|string|array<ItemInterface|string> $menu
     */
    public function get_current_item($menu): ?Item_Interface
    {
        $menu = $this->cast_menu($menu);
        return $this->retrieve_current_item($menu);
    }
    /**
     * @param ItemInterface|string|array<ItemInterface|string> $menu
     */
    private function cast_menu($menu): Item_Interface
    {
        if (!$menu instanceof Item_Interface) {
            $path = [];
            if (\is_array($menu)) {
                if (empty($menu)) {
                    throw new \InvalidArgumentException('The array cannot be empty');
                }
                $path = $menu;
                $menu = \array_shift($path);
            }
            return $this->get($menu, $path);
        }
        return $menu;
    }
    private function retrieve_current_item(Item_Interface $item): ?Item_Interface
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the current item of a menu');
        }
        if ($this->matcher->is_current($item)) {
            return $item;
        }
        if ($this->matcher->is_ancestor($item)) {
            foreach ($item->get_children() as $child) {
                $current_item = $this->retrieve_current_item($child);
                if (null !== $current_item) {
                    return $current_item;
                }
            }
        }
        return null;
    }
}