<?php

declare (strict_types=1);
namespace Knp\Menu\Util;

use Knp\Menu\Item_Interface;
class Menu_Manipulator
{
    /**
     * Moves item to specified position. Rearrange siblings accordingly.
     *
     * @param int $position position to move child to
     */
    public function move_to_position(Item_Interface $item, int $position): void
    {
        if (null !== $parent = $item->get_parent()) {
            $this->move_child_to_position($parent, $item, $position);
        }
    }
    /**
     * Moves child to specified position. Rearrange other children accordingly.
     *
     * @param ItemInterface $child    Child to move
     * @param int           $position Position to move child to
     */
    public function move_child_to_position(Item_Interface $item, Item_Interface $child, int $position): void
    {
        $name = $child->get_name();
        $order = \array_keys($item->get_children());
        $old_position = \array_search($name, $order);
        unset($order[$old_position]);
        $order = \array_values($order);
        \array_splice($order, $position, 0, $name);
        $item->reorder_children($order);
    }
    /**
     * Moves item to first position. Rearrange siblings accordingly.
     */
    public function move_to_first_position(Item_Interface $item): void
    {
        $this->move_to_position($item, 0);
    }
    /**
     * Moves item to last position. Rearrange siblings accordingly.
     */
    public function move_to_last_position(Item_Interface $item): void
    {
        if (null !== $parent = $item->get_parent()) {
            $this->move_to_position($item, $parent->count());
        }
    }
    /**
     * Get slice of menu as another menu.
     *
     * If offset and/or length are numeric, it works like in array_slice function:
     *
     *   If offset is non-negative, slice will start at the offset.
     *   If offset is negative, slice will start that far from the end.
     *
     *   If length is null, slice will have all elements.
     *   If length is positive, slice will have that many elements.
     *   If length is negative, slice will stop that far from the end.
     *
     * It's possible to mix names/object/numeric, for example:
     *   slice("child1", 2);
     *   slice(3, $child5);
     * Note: when using a child as limit, it will not be included in the returned menu.
     * the slice is done before this menu.
     *
     * @param mixed                         $offset name of child, child object, or numeric offset
     * @param string|int|ItemInterface|null $length name of child, child object, or numeric length
     */
    public function slice(Item_Interface $item, $offset, $length = null): Item_Interface
    {
        $names = \array_keys($item->get_children());
        if ($offset instanceof Item_Interface) {
            $offset = $offset->get_name();
        }
        if (!\is_int($offset)) {
            $offset = \array_search($offset, $names, true);
            if (false === $offset) {
                throw new \InvalidArgumentException('Not found.');
            }
        }
        if (null !== $length) {
            if ($length instanceof Item_Interface) {
                $length = $length->get_name();
            }
            if (!\is_int($length)) {
                $index = \array_search($length, $names, true);
                $length = $index < $offset ? 0 : $index - $offset;
            }
        }
        $sliced_item = $item->copy();
        $children = \array_slice($sliced_item->get_children(), $offset, $length);
        $sliced_item->set_children($children);
        return $sliced_item;
    }
    /**
     * Split menu into two distinct menus.
     *
     * @param string|int|ItemInterface $length name of child, child object, or numeric length
     *
     * @phpstan-return array{primary: ItemInterface, secondary: ItemInterface}
     *
     * @return array Array with two menus, with "primary" and "secondary" key
     */
    public function split(Item_Interface $item, $length): array
    {
        return ['primary' => $this->slice($item, 0, $length), 'secondary' => $this->slice($item, $length)];
    }
    /**
     * Calls a method recursively on all of the children of this item
     *
     * @example
     * $menu->callRecursively('setShowChildren', [false]);
     *
     * @param array<int|string, mixed> $arguments
     */
    public function call_recursively(Item_Interface $item, string $method, array $arguments = []): void
    {
        $item->{$method}(...$arguments);
        foreach ($item->get_children() as $child) {
            $this->call_recursively($child, $method, $arguments);
        }
    }
    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     */
    public function get_path_as_string(Item_Interface $item, string $separator = ' > '): string
    {
        $children = [];
        $obj = $item;
        do {
            $children[] = $obj->get_label();
        } while ($obj = $obj->get_parent());
        return \implode($separator, \array_reverse($children));
    }
    /**
     * @param int|null $depth the depth until which children should be exported (null means unlimited)
     *
     * @return array<string, mixed>
     */
    public function to_array(Item_Interface $item, ?int $depth = null): array
    {
        $array = ['name' => $item->get_name(), 'label' => $item->get_label(), 'uri' => $item->get_uri(), 'attributes' => $item->get_attributes(), 'labelAttributes' => $item->get_label_attributes(), 'linkAttributes' => $item->get_link_attributes(), 'childrenAttributes' => $item->get_children_attributes(), 'extras' => $item->get_extras(), 'display' => $item->is_displayed(), 'displayChildren' => $item->get_display_children(), 'current' => $item->is_current()];
        // export the children as well, unless explicitly disabled
        if (0 !== $depth) {
            $child_depth = null === $depth ? null : $depth - 1;
            $array['children'] = [];
            foreach ($item->get_children() as $key => $child) {
                $array['children'][$key] = $this->to_array($child, $child_depth);
            }
        }
        return $array;
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
     *   * [['label' => 'subItem1', 'url' => '@homepage'], ['label' => 'subItem2']]
     *
     * @param string|ItemInterface|array<int|string, mixed>|\Traversable<mixed>|null $subItem A string or array to append onto the end of the array
     *
     * @phpstan-param string|ItemInterface|array<int|string, string|int|float|null|array{label: string, uri: string|null, item: ItemInterface|null}|ItemInterface>|\Traversable<string|int|float|null|array{label: string, uri: string|null, item: ItemInterface|null}|ItemInterface>|null $subItem
     *
     * @return array<int, array<string, mixed>>
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     *
     * @throws \InvalidArgumentException if an element of the subItem is invalid
     */
    public function get_breadcrumbs_array(Item_Interface $item, $sub_item = null): array
    {
        $breadcrumbs = $this->build_breadcrumbs_array($item);
        if (null === $sub_item) {
            return $breadcrumbs;
        }
        if ($sub_item instanceof Item_Interface) {
            $breadcrumbs[] = $this->get_breadcrumbs_item($sub_item);
            return $breadcrumbs;
        }
        if (!\is_array($sub_item) && !$sub_item instanceof \Traversable) {
            $sub_item = [$sub_item];
        }
        foreach ($sub_item as $key => $value) {
            switch (true) {
                case $value instanceof Item_Interface:
                    $value = $this->get_breadcrumbs_item($value);
                    break;
                case \is_array($value):
                    // Assume we already have the appropriate array format for the element
                    break;
                case \is_int($key) && \is_string($value):
                    $value = ['label' => $value, 'uri' => null, 'item' => null];
                    break;
                case \is_scalar($value):
                    $value = ['label' => (string) $key, 'uri' => (string) $value, 'item' => null];
                    break;
                case null === $value:
                    $value = ['label' => (string) $key, 'uri' => null, 'item' => null];
                    break;
                default:
                    throw new \InvalidArgumentException(\sprintf('Invalid value supplied for the key "%s". It should be an item, an array or a scalar', $key));
            }
            $breadcrumbs[] = $value;
        }
        return $breadcrumbs;
    }
    /**
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     */
    private function build_breadcrumbs_array(Item_Interface $item): array
    {
        $breadcrumb = [];
        do {
            $breadcrumb[] = $this->get_breadcrumbs_item($item);
        } while ($item = $item->get_parent());
        return \array_reverse($breadcrumb);
    }
    /**
     * @phpstan-return array{label: string, uri: string|null, item: ItemInterface}
     */
    private function get_breadcrumbs_item(Item_Interface $item): array
    {
        return ['label' => $item->get_label(), 'uri' => $item->get_uri(), 'item' => $item];
    }
}