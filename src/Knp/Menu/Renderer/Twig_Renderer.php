<?php

declare (strict_types=1);
namespace Knp\Menu\Renderer;

use Knp\Menu\Item_Interface;
use Knp\Menu\Matcher\Matcher_Interface;
use Twig\Environment;
/**
 * @final since 3.8.0
 */
class Twig_Renderer implements Renderer_Interface
{
    /**
     * @param array<string, mixed> $defaultOptions
     */
    public function __construct(private readonly Environment $environment, string $template, private readonly Matcher_Interface $matcher, private array $default_options = [])
    {
        $this->default_options = \array_merge(['depth' => null, 'matchingDepth' => null, 'currentAsLink' => true, 'currentClass' => 'current', 'ancestorClass' => 'current_ancestor', 'firstClass' => 'first', 'lastClass' => 'last', 'template' => $template, 'compressed' => false, 'allow_safe_labels' => false, 'clear_matcher' => true, 'leaf_class' => null, 'branch_class' => null], $default_options);
    }
    public function render(Item_Interface $item, array $options = []): string
    {
        $options = \array_merge($this->default_options, $options);
        $html = $this->environment->render($options['template'], ['item' => $item, 'options' => $options, 'matcher' => $this->matcher]);
        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }
        return $html;
    }
}