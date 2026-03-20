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
    /**
     * Initialises the factory with the built-in {@see Core_Extension} at low priority (-10).
     *
     * Additional extensions may be registered via {@see add_extension()}.
     */
    public function __construct()
    {
        $this->add_extension(new Core_Extension(), -10);
    }

    /**
     * Creates a new menu item with the given name and options.
     *
     * Each registered extension's {@see Extension_Interface::build_options()} is called
     * first (in priority order) to normalise the options array, then
     * {@see Extension_Interface::build_item()} is called to populate the item's
     * properties (URI, route, extras, display, etc.).
     *
     * @param string               $name    The label/name of the menu item.
     * @param array<string, mixed> $options Options understood by registered extensions
     *                                      (e.g. 'uri', 'route', 'routeParameters', 'extras').
     *
     * @return Item_Interface The fully configured menu item.
     */
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
     * Registers a factory extension at the given priority.
     *
     * Extensions with higher priority values run first.  The built-in
     * `Core_Extension` is registered at priority -10 so user extensions take
     * precedence by default.
     *
     * @param Extension_Interface $extension The extension to register.
     * @param int                 $priority  Execution priority (higher = earlier). Default: 0.
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