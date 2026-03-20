<?php

declare (strict_types=1);
namespace Knp\Menu\Loader;

use Knp\Menu\Factory_Interface;
use Knp\Menu\Item_Interface;
/**
 * Loader importing a menu tree from an array.
 *
 * The array should match the output of MenuManipulator::toArray
 *
 * @final since 3.8.0
 */
class Array_Loader implements Loader_Interface
{
    public function __construct(private readonly Factory_Interface $factory)
    {
    }
    public function load($data): Item_Interface
    {
        if (!$this->supports($data)) {
            throw new \InvalidArgumentException(\sprintf('Unsupported data. Expected an array but got %s', \get_debug_type($data)));
        }
        return $this->from_array($data);
    }
    public function supports($data): bool
    {
        return \is_array($data);
    }
    /**
     * @param array<string, mixed> $data
     * @param string|null          $name (the name of the item, used only if there is no name in the data themselves)
     */
    private function from_array(array $data, ?string $name = null): Item_Interface
    {
        $name = $data['name'] ?? $name;
        if (isset($data['children'])) {
            $children = $data['children'];
            unset($data['children']);
        } else {
            $children = [];
        }
        $item = $this->factory->create_item($name, $data);
        foreach ($children as $child_name => $child) {
            $item->add_child($this->from_array($child, $child_name));
        }
        return $item;
    }
}