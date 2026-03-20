<?php

declare (strict_types=1);
namespace Knp\Menu;

/**
 * Interface implemented by a node to construct a menu from a tree.
 */
interface Node_Interface
{
    /**
     * Get the name of the node
     *
     * Each child of a node must have a unique name
     */
    public function get_name(): string;
    /**
     * Get the options for the factory to create the item for this node
     *
     * @return array<string, mixed>
     */
    public function get_options(): array;
    /**
     * Get the child nodes implementing NodeInterface
     *
     * @return \Traversable<int, self>
     */
    public function get_children(): \Traversable;
}