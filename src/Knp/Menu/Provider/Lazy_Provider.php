<?php

declare (strict_types=1);
namespace Knp\Menu\Provider;

use Knp\Menu\Item_Interface;
/**
 * A menu provider building menus lazily thanks to builder callables.
 *
 * Builders can either be callables or a factory for an object callable
 * represented as [Closure, method], where the Closure gets called
 * to instantiate the object.
 *
 * @final since 3.8.0
 */
class Lazy_Provider implements Menu_Provider_Interface
{
    /**
     * @phpstan-param array<string, (callable(): ItemInterface)|array{\Closure(): object, string}> $builders
     */
    public function __construct(private array $builders)
    {
    }
    public function get(string $name, array $options = []): Item_Interface
    {
        if (!isset($this->builders[$name])) {
            throw new \InvalidArgumentException(\sprintf('The menu "%s" is not defined.', $name));
        }
        $builder = $this->builders[$name];
        if (\is_array($builder) && isset($builder[0]) && $builder[0] instanceof \Closure) {
            $builder[0] = $builder[0]();
        }
        if (!\is_callable($builder)) {
            throw new \LogicException(\sprintf('Invalid menu builder for "%s". A callable or a factory for an object callable are expected.', $name));
        }
        return $builder($options);
    }
    public function has(string $name, array $options = []): bool
    {
        return isset($this->builders[$name]);
    }
}