<?php

declare (strict_types=1);
namespace Knp\Menu\Matcher;

use Knp\Menu\Item_Interface;
use Knp\Menu\Matcher\Voter\Voter_Interface;
/**
 * A MatcherInterface implementation using a voter system
 *
 * @final since 3.8.0
 */
class Matcher implements Matcher_Interface
{
    /**
     * @var \WeakMap<ItemInterface, bool>
     */
    private \WeakMap $cache;
    /**
     * @param iterable<VoterInterface> $voters
     */
    public function __construct(private readonly iterable $voters = [])
    {
        $this->cache = new \WeakMap();
    }
    public function is_current(Item_Interface $item): bool
    {
        $current = $item->is_current();
        if (null !== $current) {
            return $current;
        }
        if ($this->cache->offsetExists($item)) {
            return $this->cache[$item];
        }
        foreach ($this->voters as $voter) {
            $current = $voter->match_item($item);
            if (null !== $current) {
                break;
            }
        }
        $current = (bool) $current;
        $this->cache[$item] = $current;
        return $current;
    }
    public function is_ancestor(Item_Interface $item, ?int $depth = null): bool
    {
        if (0 === $depth) {
            return false;
        }
        $child_depth = null === $depth ? null : $depth - 1;
        foreach ($item->get_children() as $child) {
            if ($this->is_current($child) || $this->is_ancestor($child, $child_depth)) {
                return true;
            }
        }
        return false;
    }
    public function clear(): void
    {
        $this->cache = new \WeakMap();
    }
}