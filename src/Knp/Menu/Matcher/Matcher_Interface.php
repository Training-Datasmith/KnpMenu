<?php

declare (strict_types=1);
namespace Knp\Menu\Matcher;

use Knp\Menu\Item_Interface;
/**
 * Interface implemented by the item matcher
 */
interface Matcher_Interface
{
    /**
     * Checks whether an item is current.
     */
    public function is_current(Item_Interface $item): bool;
    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param int|null $depth The max depth to look for the item
     */
    public function is_ancestor(Item_Interface $item, ?int $depth = null): bool;
    /**
     * Clears the state of the matcher.
     */
    public function clear(): void;
}