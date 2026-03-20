<?php

declare (strict_types=1);
namespace Knp\Menu;

/**
 * Default implementation of the ItemInterface
 */
class Menu_Item implements Item_Interface
{
    /**
     * Label to output, name is used by default
     *
     * @var string|null
     */
    protected $label;
    /**
     * Attributes for the item link
     *
     * @var array<string, string|bool|null>
     */
    protected $link_attributes = [];
    /**
     * Attributes for the children list
     *
     * @var array<string, string|bool|null>
     */
    protected $children_attributes = [];
    /**
     * Attributes for the item text
     *
     * @var array<string, string|bool|null>
     */
    protected $label_attributes = [];
    /**
     * Uri to use in the anchor tag
     *
     * @var string|null
     */
    protected $uri;
    /**
     * Attributes for the item
     *
     * @var array<string, string|bool|null>
     */
    protected $attributes = [];
    /**
     * Extra stuff associated to the item
     *
     * @var array<string, mixed>
     */
    protected $extras = [];
    /**
     * Whether the item is displayed
     *
     * @var bool
     */
    protected $display = true;
    /**
     * Whether the children of the item are displayed
     *
     * @var bool
     */
    protected $display_children = true;
    /**
     * Child items
     *
     * @var array<string, ItemInterface>
     */
    protected $children = [];
    /**
     * Parent item
     *
     * @var ItemInterface|null
     */
    protected $parent;
    /**
     * whether the item is current. null means unknown
     *
     * @var bool|null
     */
    protected $is_current;
    /**
     * Class constructor
     *
     * @param string $name The name of this menu, which is how its parent will
     *                     reference it. Also used as label if label not specified
     */
    public function __construct(protected string $name, protected \Knp\Menu\Factory_Interface $factory)
    {
    }
    public function set_factory(Factory_Interface $factory): Item_Interface
    {
        $this->factory = $factory;
        return $this;
    }
    public function get_name(): string
    {
        return $this->name;
    }
    public function set_name(string $name): Item_Interface
    {
        if ($this->name === $name) {
            return $this;
        }
        $parent = $this->get_parent();
        if (null !== $parent && isset($parent[$name])) {
            throw new \InvalidArgumentException('Cannot rename item, name is already used by sibling.');
        }
        $old_name = $this->name;
        $this->name = $name;
        if (null !== $parent) {
            $names = \array_keys($parent->get_children());
            $items = \array_values($parent->get_children());
            $offset = \array_search($old_name, $names);
            $names[$offset] = $name;
            $children = \array_combine($names, $items);
            $parent->set_children($children);
        }
        return $this;
    }
    public function get_uri(): ?string
    {
        return $this->uri;
    }
    public function set_uri(?string $uri): Item_Interface
    {
        $this->uri = $uri;
        return $this;
    }
    public function get_label(): string
    {
        return $this->label ?? $this->name;
    }
    public function set_label(?string $label): Item_Interface
    {
        $this->label = $label;
        return $this;
    }
    public function get_attributes(): array
    {
        return $this->attributes;
    }
    public function set_attributes(array $attributes): Item_Interface
    {
        $this->attributes = $attributes;
        return $this;
    }
    public function get_attribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }
    public function set_attribute(string $name, $value): Item_Interface
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    public function get_link_attributes(): array
    {
        return $this->link_attributes;
    }
    public function set_link_attributes(array $link_attributes): Item_Interface
    {
        $this->link_attributes = $link_attributes;
        return $this;
    }
    public function get_link_attribute(string $name, $default = null)
    {
        return $this->link_attributes[$name] ?? $default;
    }
    public function set_link_attribute(string $name, $value): Item_Interface
    {
        $this->link_attributes[$name] = $value;
        return $this;
    }
    public function get_children_attributes(): array
    {
        return $this->children_attributes;
    }
    public function set_children_attributes(array $children_attributes): Item_Interface
    {
        $this->children_attributes = $children_attributes;
        return $this;
    }
    public function get_children_attribute(string $name, $default = null)
    {
        return $this->children_attributes[$name] ?? $default;
    }
    public function set_children_attribute(string $name, $value): Item_Interface
    {
        $this->children_attributes[$name] = $value;
        return $this;
    }
    public function get_label_attributes(): array
    {
        return $this->label_attributes;
    }
    public function set_label_attributes(array $label_attributes): Item_Interface
    {
        $this->label_attributes = $label_attributes;
        return $this;
    }
    public function get_label_attribute(string $name, $default = null)
    {
        return $this->label_attributes[$name] ?? $default;
    }
    public function set_label_attribute(string $name, $value): Item_Interface
    {
        $this->label_attributes[$name] = $value;
        return $this;
    }
    public function get_extras(): array
    {
        return $this->extras;
    }
    public function set_extras(array $extras): Item_Interface
    {
        $this->extras = $extras;
        return $this;
    }
    public function get_extra(string $name, $default = null)
    {
        return $this->extras[$name] ?? $default;
    }
    public function set_extra(string $name, $value): Item_Interface
    {
        $this->extras[$name] = $value;
        return $this;
    }
    public function get_display_children(): bool
    {
        return $this->display_children;
    }
    public function set_display_children(bool $bool): Item_Interface
    {
        $this->display_children = $bool;
        return $this;
    }
    public function is_displayed(): bool
    {
        return $this->display;
    }
    public function set_display(bool $bool): Item_Interface
    {
        $this->display = $bool;
        return $this;
    }
    public function add_child($child, array $options = []): Item_Interface
    {
        if (!$child instanceof Item_Interface) {
            $child = $this->factory->create_item($child, $options);
        } elseif (null !== $child->get_parent()) {
            throw new \InvalidArgumentException('Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).');
        }
        $child->set_parent($this);
        $this->children[$child->get_name()] = $child;
        return $child;
    }
    public function get_child(string $name): ?Item_Interface
    {
        return $this->children[$name] ?? null;
    }
    public function reorder_children(array $order): Item_Interface
    {
        if (\count($order) !== $this->count()) {
            throw new \InvalidArgumentException('Cannot reorder children, order does not contain all children.');
        }
        $new_children = [];
        foreach ($order as $name) {
            if (!isset($this->children[$name])) {
                throw new \InvalidArgumentException('Cannot find children named ' . $name);
            }
            $child = $this->children[$name];
            $new_children[$name] = $child;
        }
        $this->set_children($new_children);
        return $this;
    }
    public function copy(): Item_Interface
    {
        $new_menu = clone $this;
        $new_menu->set_children([]);
        $new_menu->set_parent();
        foreach ($this->get_children() as $child) {
            $new_menu->add_child($child->copy());
        }
        return $new_menu;
    }
    public function get_level(): int
    {
        return $this->parent ? $this->parent->get_level() + 1 : 0;
    }
    public function get_root(): Item_Interface
    {
        $obj = $this;
        do {
            $found = $obj;
        } while ($obj = $obj->get_parent());
        return $found;
    }
    public function is_root(): bool
    {
        return null === $this->parent;
    }
    public function get_parent(): ?Item_Interface
    {
        return $this->parent;
    }
    public function set_parent(?Item_Interface $parent = null): Item_Interface
    {
        if ($parent === $this) {
            throw new \InvalidArgumentException('Item cannot be a child of itself');
        }
        $this->parent = $parent;
        return $this;
    }
    public function get_children(): array
    {
        return $this->children;
    }
    public function set_children(array $children): Item_Interface
    {
        $this->children = $children;
        return $this;
    }
    public function remove_child($name): Item_Interface
    {
        $name = $name instanceof Item_Interface ? $name->get_name() : $name;
        if (isset($this->children[$name])) {
            // unset the child and reset it so it looks independent
            $this->children[$name]->set_parent();
            unset($this->children[$name]);
        }
        return $this;
    }
    public function get_first_child(): Item_Interface
    {
        if (empty($this->children)) {
            throw new \LogicException('Cannot get first child: there are no children.');
        }
        return \reset($this->children);
    }
    public function get_last_child(): Item_Interface
    {
        if (empty($this->children)) {
            throw new \LogicException('Cannot get last child: there are no children.');
        }
        return \end($this->children);
    }
    public function has_children(): bool
    {
        foreach ($this->children as $child) {
            if ($child->is_displayed()) {
                return true;
            }
        }
        return false;
    }
    public function set_current(?bool $bool): Item_Interface
    {
        $this->is_current = $bool;
        return $this;
    }
    public function is_current(): ?bool
    {
        return $this->is_current;
    }
    public function is_last(): bool
    {
        // if this is root, then return false
        if (null === $this->parent) {
            return false;
        }
        return $this->parent->get_last_child() === $this;
    }
    public function is_first(): bool
    {
        // if this is root, then return false
        if (null === $this->parent) {
            return false;
        }
        return $this->parent->get_first_child() === $this;
    }
    public function acts_like_first(): bool
    {
        // root items are never "marked" as first
        if (null === $this->parent) {
            return false;
        }
        // A menu acts like first only if it is displayed
        if (!$this->is_displayed()) {
            return false;
        }
        // if we're first and visible, we're first, period.
        if ($this->is_first()) {
            return true;
        }
        $children = $this->parent->get_children();
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->is_displayed()) {
                return $child->get_name() === $this->get_name();
            }
        }
        return false;
    }
    public function acts_like_last(): bool
    {
        // root items are never "marked" as last
        if (null === $this->parent) {
            return false;
        }
        // A menu acts like last only if it is displayed
        if (!$this->is_displayed()) {
            return false;
        }
        // if we're last and visible, we're last, period.
        if ($this->is_last()) {
            return true;
        }
        $children = \array_reverse($this->parent->get_children());
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->is_displayed()) {
                return $child->get_name() === $this->get_name();
            }
        }
        return false;
    }
    /**
     * Implements Countable
     */
    public function count(): int
    {
        return \count($this->children);
    }
    /**
     * Implements IteratorAggregate
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->children);
    }
    /**
     * Implements ArrayAccess
     *
     * @param string $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->children[$offset]);
    }
    /**
     * Implements ArrayAccess
     *
     * @param string $offset
     *
     * @return ItemInterface|null
     */
    #[\Return_Type_Will_Change]
    public function offsetGet($offset)
    {
        return $this->get_child($offset);
    }
    /**
     * Implements ArrayAccess
     *
     * @param string      $offset
     * @param string|null $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->add_child($offset)->set_label($value);
    }
    /**
     * Implements ArrayAccess
     *
     * @param string $offset
     */
    public function offsetUnset($offset): void
    {
        $this->remove_child($offset);
    }
}