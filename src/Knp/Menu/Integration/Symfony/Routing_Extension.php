<?php

declare (strict_types=1);
namespace Knp\Menu\Integration\Symfony;

use Knp\Menu\Factory\Extension_Interface;
use Knp\Menu\Item_Interface;
use Symfony\Component\Routing\Generator\Url_Generator_Interface;
/**
 * Factory able to use the Symfony Routing component to build the url
 *
 * @final since 3.8.0
 */
class Routing_Extension implements Extension_Interface
{
    public function __construct(private readonly Url_Generator_Interface $generator)
    {
    }
    public function build_options(array $options = []): array
    {
        if (!empty($options['route'])) {
            $params = $options['routeParameters'] ?? [];
            $absolute = isset($options['routeAbsolute']) && $options['routeAbsolute'] ? Url_Generator_Interface::ABSOLUTE_URL : Url_Generator_Interface::ABSOLUTE_PATH;
            $options['uri'] = $this->generator->generate($options['route'], $params, $absolute);
            // adding the item route to the extras under the 'routes' key (for the Silex RouteVoter)
            $options['extras']['routes'][] = ['route' => $options['route'], 'parameters' => $params];
        }
        return $options;
    }
    public function build_item(Item_Interface $item, array $options): void
    {
    }
}