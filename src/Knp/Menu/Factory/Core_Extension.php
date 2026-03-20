<?php

declare (strict_types=1);
namespace Knp\Menu\Factory;

use Knp\Menu\Item_Interface;
/**
 * core factory extension with the main logic
 *
 * @final since 3.8.0
 * @internal since 3.8.0
 */
class Core_Extension implements Extension_Interface
{
    public function build_options(array $options): array
    {
        return \array_merge(['uri' => null, 'label' => null, 'attributes' => [], 'linkAttributes' => [], 'childrenAttributes' => [], 'labelAttributes' => [], 'extras' => [], 'current' => null, 'display' => true, 'displayChildren' => true], $options);
    }
    public function build_item(Item_Interface $item, array $options): void
    {
        $item->set_uri($options['uri'])->set_label($options['label'])->set_attributes($options['attributes'])->set_link_attributes($options['linkAttributes'])->set_children_attributes($options['childrenAttributes'])->set_label_attributes($options['labelAttributes'])->set_current($options['current'])->set_display($options['display'])->set_display_children($options['displayChildren']);
        $this->build_extras($item, $options);
    }
    /**
     * Configures the newly created item's extras
     * Extras are processed one by one in order not to reset values set by other extensions
     *
     * @param array<string, array<string, mixed>> $options
     */
    private function build_extras(Item_Interface $item, array $options): void
    {
        foreach ($options['extras'] as $key => $value) {
            $item->set_extra($key, $value);
        }
    }
}