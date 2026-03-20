<?php

declare (strict_types=1);
namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\Item_Interface;
/**
 * Interface implemented by the matching voters
 */
interface Voter_Interface
{
    /**
     * Checks whether an item is current.
     *
     * If the voter is not able to determine a result,
     * it should return null to let other voters do the job.
     */
    public function match_item(Item_Interface $item): ?bool;
}