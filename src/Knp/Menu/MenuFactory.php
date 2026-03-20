<?php

declare (strict_types=1);
namespace Knp\Menu;

use Knp\Menu\Factory\Core_Extension;
use Knp\Menu\Factory\Extension_Interface;
/**
 * Factory to create a menu from a tree
 *
 * @final since 3.8.0
 */
class Menu_Factory implements Factory_Interface
{
    /**
     * @var array<int, list<ExtensionInterface>>
     */
    private array $extensions = [];
    /**
     * @var ExtensionInterface[]|null
     */
    private ?array $sorted = null;
    public function __construct()
    {
        $this->add_extension(new Core_Extension(), -10);
    }
    public function create_item(string $name, array $options = []): Item_Interface
    {
        foreach ($this->get_extensions() as $extension) {
            $options = $extension->build_options($options);
        }
        $item = new Menu_Item($name, $this);
        foreach ($this->get_extensions() as $extension) {
            $extension->build_item($item, $options);
        }
        return $item;
    }
    /**
     * Adds a factory extension
     */
    public function add_extension(Extension_Interface $extension, int $priority = 0): void
    {
        $this->extensions[$priority][] = $extension;
        $this->sorted = null;
    }
    /**
     * Sorts the internal list of extensions by priority.
     *
     * @return ExtensionInterface[]
     */
    private function get_extensions(): array
    {
        if (null === $this->sorted) {
            \krsort($this->extensions);
            $this->sorted = !empty($this->extensions) ? \array_merge(...$this->extensions) : [];
        }
        return $this->sorted;
    }
}