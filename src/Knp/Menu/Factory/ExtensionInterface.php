<?php

declare (strict_types=1);
namespace Knp\Menu\Factory;

use Knp\Menu\Item_Interface;
interface Extension_Interface
{
    /**
     * Builds the full option array used to configure the item.
     *
     * @param array<string, mixed> $options The options processed by the previous extensions
     *
     * @return array<string, mixed>
     */
    public function build_options(array $options): array;
    /**
     * Configures the item with the passed options
     *
     * @param array<string, mixed> $options
     */
    public function build_item(Item_Interface $item, array $options): void;
}