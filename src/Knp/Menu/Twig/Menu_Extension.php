<?php

declare (strict_types=1);
namespace Knp\Menu\Twig;

use Knp\Menu\Item_Interface;
use Knp\Menu\Matcher\Matcher_Interface;
use Knp\Menu\Util\Menu_Manipulator;
use Twig\Extension\Abstract_Extension;
use Twig\Twig_Filter;
use Twig\Twig_Function;
use Twig\Twig_Test;
/**
 * @final since 3.8.0
 */
class Menu_Extension extends Abstract_Extension
{
    private ?Menu_Runtime_Extension $runtime_extension = null;
    public function __construct(?Helper $helper = null, ?Matcher_Interface $matcher = null, ?Menu_Manipulator $menu_manipulator = null)
    {
        if (null !== $helper) {
            @trigger_error('Injecting dependencies is deprecated since v3.6 and will be removed in v4.', E_USER_DEPRECATED);
            $this->runtime_extension = new Menu_Runtime_Extension($helper, $matcher, $menu_manipulator);
        }
    }
    public function get_functions(): array
    {
        $legacy = null !== $this->runtime_extension;
        return [new Twig_Function('knp_menu_get', $legacy ? $this->get(...) : [Menu_Runtime_Extension::class, 'get']), new Twig_Function('knp_menu_render', $legacy ? $this->render(...) : [Menu_Runtime_Extension::class, 'render'], ['is_safe' => ['html']]), new Twig_Function('knp_menu_get_breadcrumbs_array', $legacy ? $this->get_breadcrumbs_array(...) : [Menu_Runtime_Extension::class, 'getBreadcrumbsArray']), new Twig_Function('knp_menu_get_current_item', $legacy ? $this->get_current_item(...) : [Menu_Runtime_Extension::class, 'getCurrentItem'])];
    }
    public function get_filters(): array
    {
        $legacy = null !== $this->runtime_extension;
        return [new Twig_Filter('knp_menu_as_string', $legacy ? $this->path_as_string(...) : [Menu_Runtime_Extension::class, 'pathAsString']), new Twig_Filter('knp_menu_spaceless', self::spaceless(...), ['is_safe' => ['html']])];
    }
    public function get_tests(): array
    {
        $legacy = null !== $this->runtime_extension;
        return [new Twig_Test('knp_menu_current', $legacy ? $this->is_current(...) : [Menu_Runtime_Extension::class, 'isCurrent']), new Twig_Test('knp_menu_ancestor', $legacy ? $this->is_ancestor(...) : [Menu_Runtime_Extension::class, 'isAncestor'])];
    }
    public function get_last_modified(): int
    {
        return max((int) filemtime(__FILE__), (int) filemtime(__DIR__ . '/MenuRuntimeExtension.php'));
    }
    /**
     * @param array<int, string>   $path
     * @param array<string, mixed> $options
     *
     * @internal since 3.8.0
     */
    public function get(Item_Interface|string $menu, array $path = [], array $options = []): Item_Interface
    {
        assert(null !== $this->runtime_extension);
        return $this->runtime_extension->get($menu, $path, $options);
    }
    /**
     * @param string|ItemInterface|array<ItemInterface|string> $menu
     * @param array<string, mixed>                             $options
     *
     * @internal since 3.8.0
     */
    public function render(array|Item_Interface|string $menu, array $options = [], ?string $renderer = null): string
    {
        assert(null !== $this->runtime_extension);
        return $this->runtime_extension->render($menu, $options, $renderer);
    }
    /**
     * @param string|ItemInterface|array<ItemInterface|string> $menu
     *
     * @phpstan-param string|array<int|string, string|int|float|null|array{label: string, uri: string|null, item: ItemInterface|null}|ItemInterface> $subItem
     *
     * @return array<int, array<string, mixed>>
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     *
     * @internal since 3.8.0
     */
    public function get_breadcrumbs_array(array|Item_Interface|string $menu, array|string|null $sub_item = null): array
    {
        assert(null !== $this->runtime_extension);
        return $this->runtime_extension->get_breadcrumbs_array($menu, $sub_item);
    }
    /**
     * @internal since 3.8.0
     */
    public function get_current_item(Item_Interface|string $menu): Item_Interface
    {
        assert(null !== $this->runtime_extension);
        return $this->runtime_extension->get_current_item($menu);
    }
    /**
     * @internal since 3.8.0
     */
    public function path_as_string(Item_Interface $menu, string $separator = ' > '): string
    {
        assert(null !== $this->runtime_extension);
        return $this->runtime_extension->path_as_string($menu, $separator);
    }
    /**
     * @internal since 3.8.0
     */
    public function is_current(Item_Interface $item): bool
    {
        assert(null !== $this->runtime_extension);
        return $this->runtime_extension->is_current($item);
    }
    /**
     * @internal since 3.8.0
     */
    public function is_ancestor(Item_Interface $item, ?int $depth = null): bool
    {
        assert(null !== $this->runtime_extension);
        return $this->runtime_extension->is_ancestor($item, $depth);
    }
    /**
     * @internal
     */
    public static function spaceless(string $content): string
    {
        return trim((string) preg_replace('/>\s+</', '><', $content));
    }
}