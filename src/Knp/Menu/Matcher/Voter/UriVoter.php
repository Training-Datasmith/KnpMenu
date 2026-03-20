<?php

declare (strict_types=1);
namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\Item_Interface;
/**
 * Voter based on the uri
 *
 * @final since 3.8.0
 */
class Uri_Voter implements Voter_Interface
{
    public function __construct(private readonly ?string $uri = null)
    {
    }
    public function match_item(Item_Interface $item): ?bool
    {
        if (null === $this->uri || null === $item->get_uri()) {
            return null;
        }
        if ($item->get_uri() === $this->uri) {
            return true;
        }
        return null;
    }
}