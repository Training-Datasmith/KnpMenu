<?php

declare (strict_types=1);
namespace Knp\Menu\Iterator;

use Knp\Menu\Item_Interface;
/**
 * Recursive iterator iterating on an item
 *
 * @template TKey
 *
 * @extends \IteratorIterator<TKey, ItemInterface, \Traversable<TKey, ItemInterface>>
 *
 * @implements \RecursiveIterator<TKey, ItemInterface>
 *
 * @final since 3.8.0
 */
class Recursive_Item_Iterator extends \Iterator_Iterator implements \Recursive_Iterator
{
    /**
     * @param \Traversable<TKey, ItemInterface> $iterator
     */
    final public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);
    }
    public function has_children(): bool
    {
        return 0 < \count($this->current());
    }
    /**
     * @return RecursiveItemIterator<TKey>
     */
    #[\Return_Type_Will_Change]
    public function get_children()
    {
        return new static($this->current());
    }
}