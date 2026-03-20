<?php

declare (strict_types=1);
namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\Item_Interface;
/**
 * Voter based on a callback
 */
final class Callback_Voter implements Voter_Interface
{
    public function match_item(Item_Interface $item): ?bool
    {
        $callback = $item->get_extra('match_callback');
        if (null === $callback) {
            return null;
        }
        if (!\is_callable($callback)) {
            throw new \InvalidArgumentException('Extra "match_callback" must be callable.');
        }
        return $callback();
    }
}