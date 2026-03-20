<?php

declare (strict_types=1);
namespace Knp\Menu\Renderer;

abstract class Renderer
{
    protected string $charset = 'UTF-8';
    public function __construct(?string $charset = null)
    {
        if (null !== $charset) {
            $this->charset = $charset;
        }
    }
    /**
     * Renders an HTML attribute
     *
     * @param string|bool $value
     */
    protected function render_html_attribute(string $name, $value): string
    {
        if (true === $value) {
            return \sprintf('%s="%s"', $name, $this->escape($name));
        }
        if (false === $value) {
            throw new \InvalidArgumentException('Value cannot be false.');
        }
        return \sprintf('%s="%s"', $name, $this->escape($value));
    }
    /**
     * Renders HTML attributes
     *
     * @param array<string, string|bool|null> $attributes
     */
    protected function render_html_attributes(array $attributes): string
    {
        return \implode('', \array_map($this->html_attributes_callback(...), \array_keys($attributes), \array_values($attributes)));
    }
    /**
     * Prepares an attribute key and value for HTML representation.
     *
     * It removes empty attributes.
     *
     * @param string           $name  The attribute name
     * @param string|bool|null $value The attribute value
     *
     * @return string the HTML representation of the HTML key attribute pair
     */
    private function html_attributes_callback(string $name, $value): string
    {
        if (false === $value || null === $value) {
            return '';
        }
        return ' ' . $this->render_html_attribute($name, $value);
    }
    /**
     * Escapes an HTML value
     */
    protected function escape(string $value): string
    {
        return $this->fix_double_escape(\htmlspecialchars($value, \ENT_QUOTES | \ENT_SUBSTITUTE, $this->charset));
    }
    /**
     * Fixes double escaped strings.
     *
     * @param string $escaped string to fix
     *
     * @return string A single escaped string
     */
    protected function fix_double_escape(string $escaped): string
    {
        return (string) \preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
    }
    /**
     * Get the HTML charset
     */
    public function get_charset(): string
    {
        return $this->charset;
    }
    /**
     * Set the HTML charset
     */
    public function set_charset(string $charset): void
    {
        $this->charset = $charset;
    }
}