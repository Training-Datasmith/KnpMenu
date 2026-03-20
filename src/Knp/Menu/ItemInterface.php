<?php

declare (strict_types=1);
namespace Knp\Menu;

/**
 * Interface implemented by a menu item.
 *
 * It roughly represents a single <li> tag and is what you should interact with
 * most of the time by default.
 * Originally taken from ioMenuPlugin (http://github.com/weaverryan/ioMenuPlugin)
 *
 * @extends \ArrayAccess<string, self|null>
 * @extends \IteratorAggregate<string, self>
 */
interface Item_Interface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    public function set_factory(Factory_Interface $factory): self;
    public function get_name(): string;
    /**
     * Renames the item.
     *
     * This method must also update the key in the parent.
     *
     * Provides a fluent interface
     *
     * @throws \InvalidArgumentException if the name is already used by a sibling
     */
    public function set_name(string $name): self;
    /**
     * Get the uri for a menu item
     */
    public function get_uri(): ?string;
    /**
     * Set the uri for a menu item
     *
     * Provides a fluent interface
     *
     * @param string|null $uri The uri to set on this menu item
     */
    public function set_uri(?string $uri): self;
    /**
     * Returns the label that will be used to render this menu item
     *
     * Defaults to the name of no label was specified
     */
    public function get_label(): string;
    /**
     * Provides a fluent interface
     *
     * @param string|null $label The text to use when rendering this menu item
     */
    public function set_label(?string $label): self;
    /**
     * @return array<string, string|bool|null>
     */
    public function get_attributes(): array;
    /**
     * @param array<string, string|bool|null> $attributes
     */
    public function set_attributes(array $attributes): self;
    /**
     * @param string           $name    The name of the attribute to return
     * @param string|bool|null $default The value to return if the attribute doesn't exist
     *
     * @return string|bool|null
     */
    public function get_attribute(string $name, $default = null);
    /**
     * @param string|bool|null $value
     */
    public function set_attribute(string $name, $value): self;
    /**
     * @return array<string, string|bool|null>
     */
    public function get_link_attributes(): array;
    /**
     * @param array<string, string|bool|null> $linkAttributes
     */
    public function set_link_attributes(array $link_attributes): self;
    /**
     * @param string           $name    The name of the attribute to return
     * @param string|bool|null $default The value to return if the attribute doesn't exist
     *
     * @return string|bool|null
     */
    public function get_link_attribute(string $name, $default = null);
    /**
     * @param string|bool|null $value
     */
    public function set_link_attribute(string $name, $value): self;
    /**
     * @return array<string, string|bool|null>
     */
    public function get_children_attributes(): array;
    /**
     * @param array<string, string|bool|null> $childrenAttributes
     */
    public function set_children_attributes(array $children_attributes): self;
    /**
     * @param string           $name    The name of the attribute to return
     * @param string|bool|null $default The value to return if the attribute doesn't exist
     *
     * @return string|bool|null
     */
    public function get_children_attribute(string $name, $default = null);
    /**
     * @param string|bool|null $value
     */
    public function set_children_attribute(string $name, $value): self;
    /**
     * @return array<string, string|bool|null>
     */
    public function get_label_attributes(): array;
    /**
     * @param array<string, string|bool|null> $labelAttributes
     */
    public function set_label_attributes(array $label_attributes): self;
    /**
     * @param string           $name    The name of the attribute to return
     * @param string|bool|null $default The value to return if the attribute doesn't exist
     *
     * @return string|bool|null
     */
    public function get_label_attribute(string $name, $default = null);
    /**
     * @param string|bool|null $value
     */
    public function set_label_attribute(string $name, $value): self;
    /**
     * @return array<string, mixed>
     */
    public function get_extras(): array;
    /**
     * @param array<string, mixed> $extras
     */
    public function set_extras(array $extras): self;
    /**
     * @param string $name    The name of the extra to return
     * @param mixed  $default The value to return if the extra doesn't exist
     *
     * @return mixed
     */
    public function get_extra(string $name, $default = null);
    /**
     * @param mixed $value
     */
    public function set_extra(string $name, $value): self;
    public function get_display_children(): bool;
    /**
     * Set whether or not this menu item should show its children
     *
     * Provides a fluent interface
     */
    public function set_display_children(bool $bool): self;
    /**
     * Whether or not to display this menu item
     */
    public function is_displayed(): bool;
    /**
     * Set whether or not this menu should be displayed
     *
     * Provides a fluent interface
     */
    public function set_display(bool $bool): self;
    /**
     * Add a child menu item to this menu
     *
     * Returns the child item
     *
     * @param ItemInterface|string $child   An ItemInterface instance or the name of a new item to create
     * @param array<string, mixed> $options If creating a new item, the options passed to the factory for the item
     *
     * @throws \InvalidArgumentException if the item is already in a tree
     */
    public function add_child($child, array $options = []): self;
    /**
     * Returns the child menu identified by the given name
     *
     * @param string $name Then name of the child menu to return
     */
    public function get_child(string $name): ?self;
    /**
     * Reorder children.
     *
     * Provides a fluent interface
     *
     * @param array<int|string, string> $order new order of children
     */
    public function reorder_children(array $order): self;
    /**
     * Makes a deep copy of menu tree. Every item is copied as another object.
     */
    public function copy(): self;
    /**
     * Returns the level of this menu item
     *
     * The root menu item is 0, followed by 1, 2, etc
     */
    public function get_level(): int;
    /**
     * Returns the root ItemInterface of this menu tree
     */
    public function get_root(): self;
    /**
     * Returns whether or not this menu item is the root menu item
     */
    public function is_root(): bool;
    public function get_parent(): ?self;
    /**
     * Used internally when adding and removing children
     *
     * Provides a fluent interface
     */
    public function set_parent(?self $parent = null): self;
    /**
     * Return the children as an array of ItemInterface objects
     *
     * @return array<string, self>
     */
    public function get_children(): array;
    /**
     * Provides a fluent interface
     *
     * @param array<string, self> $children An array of ItemInterface objects
     */
    public function set_children(array $children): self;
    /**
     * Removes a child from this menu item
     *
     * Provides a fluent interface
     *
     * @param ItemInterface|string $name The name of ItemInterface instance or the ItemInterface to remove
     */
    public function remove_child($name): self;
    public function get_first_child(): self;
    public function get_last_child(): self;
    /**
     * Returns whether or not this menu items has viewable children
     *
     * This menu MAY have children, but this will return false if the current
     * user does not have access to view any of those items
     */
    public function has_children(): bool;
    /**
     * Sets whether or not this menu item is "current".
     *
     * If the state is unknown, use null.
     *
     * Provides a fluent interface
     *
     * @param bool|null $bool Specify that this menu item is current
     */
    public function set_current(?bool $bool): self;
    /**
     * Gets whether or not this menu item is "current".
     */
    public function is_current(): ?bool;
    /**
     * Whether this menu item is last in its parent
     */
    public function is_last(): bool;
    /**
     * Whether this menu item is first in its parent
     */
    public function is_first(): bool;
    /**
     * Whereas isFirst() returns if this is the first child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the first child that would be rendered
     * for the current user
     */
    public function acts_like_first(): bool;
    /**
     * Whereas isLast() returns if this is the last child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the last child that would be rendered
     * for the current user
     */
    public function acts_like_last(): bool;
}