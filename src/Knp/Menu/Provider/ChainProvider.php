<?php

namespace Knp\Menu\Provider;

use Knp\Menu\ItemInterface;

/**
 * @final since 3.8.0
 */
class ChainProvider implements MenuProviderInterface
{
    /**
     * @param iterable<MenuProviderInterface> $providers
     */
    public function __construct(private readonly iterable $providers)
    {
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($name, $options)) {
                return $provider->get($name, $options);
            }
        }

        throw new \InvalidArgumentException(\sprintf('The menu "%s" is not defined.', $name));
    }

    public function has(string $name, array $options = []): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($name, $options)) {
                return true;
            }
        }

        return false;
    }
}
