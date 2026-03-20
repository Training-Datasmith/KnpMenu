<?php

declare (strict_types=1);
namespace Knp\Menu\Iterator;

/**
 * Filter iterator keeping only current items
 *
 * @final since 3.8.0
 */
class Displayed_Item_Filter_Iterator extends \Recursive_Filter_Iterator
{
    /**
     * @return bool
     */
    #[\Return_Type_Will_Change]
    public function accept()
    {
        return $this->current()->is_displayed();
    }
    /**
     * @return bool
     */
    #[\Return_Type_Will_Change]
    public function has_children()
    {
        return $this->current()->get_display_children() && parent::has_children();
    }
}