<?php

declare (strict_types=1);
namespace Knp\Menu;

/**
 * Interface implemented by the factory to create items
 */
interface Factory_Interface
{
    /**
     * Creates a menu item
     *
     * @param array<string, mixed> $options
     */
    public function create_item(string $name, array $options = []): Item_Interface;
}