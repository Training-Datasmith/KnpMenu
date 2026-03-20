<?php

declare (strict_types=1);
namespace Knp\Menu\Renderer;

use Knp\Menu\Item_Interface;
use Knp\Menu\Matcher\Matcher_Interface;
/**
 * Renders MenuItem tree as unordered list
 *
 * @final since 3.8.0
 */
class List_Renderer extends Renderer implements Renderer_Interface
{
    /**
     * @param array<string, mixed> $defaultOptions
     */
    public function __construct(protected Matcher_Interface $matcher, protected array $default_options = [], ?string $charset = null)
    {
        $this->default_options = \array_merge(['depth' => null, 'matchingDepth' => null, 'currentAsLink' => true, 'currentClass' => 'current', 'ancestorClass' => 'current_ancestor', 'firstClass' => 'first', 'lastClass' => 'last', 'compressed' => false, 'allow_safe_labels' => false, 'clear_matcher' => true, 'leaf_class' => null, 'branch_class' => null], $default_options);
        parent::__construct($charset);
    }
    public function render(Item_Interface $item, array $options = []): string
    {
        $options = \array_merge($this->default_options, $options);
        $html = $this->render_list($item, $item->get_children_attributes(), $options);
        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }
        return $html;
    }
    /**
     * @param array<string, string|bool|null> $attributes
     * @param array<string, mixed>            $options
     */
    protected function render_list(Item_Interface $item, array $attributes, array $options): string
    {
        /*
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (0 === $options['depth'] || !$item->has_children() || !$item->get_display_children()) {
            return '';
        }
        $html = $this->format('<ul' . $this->render_html_attributes($attributes) . '>', 'ul', $item->get_level(), $options);
        $html .= $this->render_children($item, $options);
        return $html . $this->format('</ul>', 'ul', $item->get_level(), $options);
    }
    /**
     * Renders all of the children of this menu.
     *
     * This calls ->renderItem() on each menu item, which instructs each
     * menu item to render themselves as an <li> tag (with nested ul if it
     * has children).
     * This method updates the depth for the children.
     *
     * @param array<string, mixed> $options the options to render the item
     */
    protected function render_children(Item_Interface $item, array $options): string
    {
        // render children with a depth - 1
        if (null !== $options['depth']) {
            --$options['depth'];
        }
        if (null !== $options['matchingDepth'] && $options['matchingDepth'] > 0) {
            --$options['matchingDepth'];
        }
        $html = '';
        foreach ($item->get_children() as $child) {
            $html .= $this->render_item($child, $options);
        }
        return $html;
    }
    /**
     * Called by the parent menu item to render this menu.
     *
     * This renders the li tag to fit into the parent ul as well as its
     * own nested ul tag if this menu item has children
     *
     * @param array<string, mixed> $options The options to render the item
     */
    protected function render_item(Item_Interface $item, array $options): string
    {
        // if we don't have access or this item is marked to not be shown
        if (!$item->is_displayed()) {
            return '';
        }
        // create an array than can be imploded as a class list
        $class = (array) $item->get_attribute('class');
        if ($this->matcher->is_current($item)) {
            $class[] = $options['currentClass'];
        } elseif ($this->matcher->is_ancestor($item, $options['matchingDepth'])) {
            $class[] = $options['ancestorClass'];
        }
        if ($item->acts_like_first()) {
            $class[] = $options['firstClass'];
        }
        if ($item->acts_like_last()) {
            $class[] = $options['lastClass'];
        }
        if (0 !== $options['depth'] && $item->has_children()) {
            if (null !== $options['branch_class'] && $item->get_display_children()) {
                $class[] = $options['branch_class'];
            }
        } elseif (null !== $options['leaf_class']) {
            $class[] = $options['leaf_class'];
        }
        // retrieve the attributes and put the final class string back on it
        $attributes = $item->get_attributes();
        if (!empty($class)) {
            $attributes['class'] = \implode(' ', $class);
        }
        // opening li tag
        $html = $this->format('<li' . $this->render_html_attributes($attributes) . '>', 'li', $item->get_level(), $options);
        // render the text/link inside the li tag
        // $html .= $this->format($item->getUri() ? $item->renderLink() : $item->renderLabel(), 'link', $item->getLevel());
        $html .= $this->render_link($item, $options);
        // renders the embedded ul
        $children_class = (array) $item->get_children_attribute('class');
        $children_class[] = 'menu_level_' . $item->get_level();
        $children_attributes = $item->get_children_attributes();
        $children_attributes['class'] = \implode(' ', $children_class);
        $html .= $this->render_list($item, $children_attributes, $options);
        // closing li tag
        $html .= $this->format('</li>', 'li', $item->get_level(), $options);
        return $html;
    }
    /**
     * Renders the link in a a tag with link attributes or
     * the label in a span tag with label attributes
     *
     * Tests if item has a an uri and if not tests if it's
     * the current item and if the text has to be rendered
     * as a link or not.
     *
     * @param ItemInterface        $item    The item to render the link or label for
     * @param array<string, mixed> $options The options to render the item
     */
    protected function render_link(Item_Interface $item, array $options = []): string
    {
        if (null !== $item->get_uri() && (!$this->matcher->is_current($item) || $options['currentAsLink'])) {
            $text = $this->render_link_element($item, $options);
        } else {
            $text = $this->render_span_element($item, $options);
        }
        return $this->format($text, 'link', $item->get_level(), $options);
    }
    /**
     * @param array<string, mixed> $options
     */
    protected function render_link_element(Item_Interface $item, array $options): string
    {
        \assert(null !== $item->get_uri());
        return \sprintf('<a href="%s"%s>%s</a>', $this->escape_uri($item->get_uri()), $this->render_html_attributes($item->get_link_attributes()), $this->render_label($item, $options));
    }
    /**
     * Escapes a URI for use in an href attribute, blocking javascript: and data: schemes.
     */
    protected function escape_uri(string $uri): string
    {
        $scheme = \strtolower(\explode(':', $uri, 2)[0] ?? '');
        $scheme = \preg_replace('/[\x00-\x20]/', '', $scheme) ?? $scheme;
        if (\in_array($scheme, ['javascript', 'data', 'vbscript'], true)) {
            return '';
        }
        return $this->escape($uri);
    }
    /**
     * @param array<string, mixed> $options
     */
    protected function render_span_element(Item_Interface $item, array $options): string
    {
        return \sprintf('<span%s>%s</span>', $this->render_html_attributes($item->get_label_attributes()), $this->render_label($item, $options));
    }
    /**
     * @param array<string, mixed> $options
     */
    protected function render_label(Item_Interface $item, array $options): string
    {
        if ($options['allow_safe_labels'] && $item->get_extra('safe_label', false)) {
            return $item->get_label();
        }
        return $this->escape($item->get_label());
    }
    /**
     * If $this->renderCompressed is on, this will apply the necessary
     * spacing and line-breaking so that the particular thing being rendered
     * makes up its part in a fully-rendered and spaced menu.
     *
     * @param string               $html    The html to render in an (un)formatted way
     * @param string               $type    The type [ul,link,li] of thing being rendered
     * @param array<string, mixed> $options
     */
    protected function format(string $html, string $type, int $level, array $options): string
    {
        if ($options['compressed']) {
            return $html;
        }
        $spacing = 0;
        switch ($type) {
            case 'ul':
            case 'link':
                $spacing = $level * 4;
                break;
            case 'li':
                $spacing = $level * 4 - 2;
        }
        return \str_repeat(' ', $spacing) . $html . "\n";
    }
}