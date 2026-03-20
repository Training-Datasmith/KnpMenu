<?php

declare (strict_types=1);
namespace Knp\Menu\Loader;

use Knp\Menu\Factory_Interface;
use Knp\Menu\Item_Interface;
use Knp\Menu\Node_Interface;
/**
 * @final since 3.8.0
 */
class Node_Loader implements Loader_Interface
{
    public function __construct(private readonly Factory_Interface $factory)
    {
    }
    public function load($data): Item_Interface
    {
        if (!$data instanceof Node_Interface) {
            throw new \InvalidArgumentException(\sprintf('Unsupported data. Expected Knp\Menu\NodeInterface but got %s', \get_debug_type($data)));
        }
        $item = $this->factory->create_item($data->get_name(), $data->get_options());
        foreach ($data->get_children() as $child_node) {
            $item->add_child($this->load($child_node));
        }
        return $item;
    }
    public function supports($data): bool
    {
        return $data instanceof Node_Interface;
    }
}