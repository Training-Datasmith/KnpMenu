<?php

declare (strict_types=1);
namespace Knp\Menu\Renderer;

use Knp\Menu\Item_Interface;
interface Renderer_Interface
{
    /**
     * Renders menu tree.
     *
     * Common options:
     *      - depth: The depth at which the item is rendered
     *          null: no limit
     *          0: no children
     *          1: only direct children
     *      - currentAsLink: whether the current item should be a link
     *      - currentClass: class added to the current item
     *      - ancestorClass: class added to the ancestors of the current item
     *      - firstClass: class added to the first child
     *      - lastClass: class added to the last child
     *
     * @param ItemInterface        $item    Menu item
     * @param array<string, mixed> $options some rendering options
     */
    public function render(Item_Interface $item, array $options = []): string;
}