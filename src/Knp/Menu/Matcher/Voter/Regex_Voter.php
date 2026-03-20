<?php

declare (strict_types=1);
namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\Item_Interface;
/**
 * Implements the VoterInterface which can be used as voter for "current" class
 * `matchItem` will return true if the pattern you're searching for is found in the URI of the item
 *
 * @final since 3.8.0
 */
class Regex_Voter implements Voter_Interface
{
    public function __construct(private readonly ?string $regexp)
    {
    }
    public function match_item(Item_Interface $item): ?bool
    {
        if (null === $this->regexp || null === $item->get_uri()) {
            return null;
        }
        if (\preg_match($this->regexp, $item->get_uri())) {
            return true;
        }
        return null;
    }
}