<?php

declare (strict_types=1);
namespace Knp\Menu\Loader;

use Knp\Menu\Item_Interface;
interface Loader_Interface
{
    /**
     * Loads the data into a menu item
     *
     * @param mixed $data
     */
    public function load($data): Item_Interface;
    /**
     * Checks whether the loader can load these data
     *
     * @param mixed $data
     */
    public function supports($data): bool;
}