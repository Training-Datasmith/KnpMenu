<?php

declare (strict_types=1);
namespace Knp\Menu\Iterator;

use Knp\Menu\Item_Interface;
use Knp\Menu\Matcher\Matcher_Interface;
/**
 * Filter iterator keeping only current items
 *
 * @template TKey
 * @template-extends \FilterIterator<TKey, ItemInterface, \Iterator<TKey, ItemInterface>>
 *
 * @final since 3.8.0
 */
class Current_Item_Filter_Iterator extends \Filter_Iterator
{
    /**
     * @param \Iterator<TKey, ItemInterface> $iterator
     */
    public function __construct(\Iterator $iterator, private readonly Matcher_Interface $matcher)
    {
        parent::__construct($iterator);
    }
    /**
     * @return bool
     */
    #[\Return_Type_Will_Change]
    public function accept()
    {
        return $this->matcher->is_current($this->current());
    }
}