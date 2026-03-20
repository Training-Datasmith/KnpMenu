<?php

declare (strict_types=1);
namespace Knp\Menu\Provider;

use Knp\Menu\Item_Interface;
interface Menu_Provider_Interface
{
    /**
     * Retrieves a menu by its name
     *
     * @param array<string, mixed> $options
     *
     * @throws \InvalidArgumentException if the menu does not exist
     */
    public function get(string $name, array $options = []): Item_Interface;
    /**
     * Checks whether a menu exists in this provider
     *
     * @param array<string, mixed> $options
     */
    public function has(string $name, array $options = []): bool;
}