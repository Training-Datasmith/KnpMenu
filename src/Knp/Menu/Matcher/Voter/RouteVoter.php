<?php

declare (strict_types=1);
namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\Item_Interface;
use Symfony\Component\Http_Foundation\Request;
use Symfony\Component\Http_Foundation\Request_Stack;
/**
 * Voter based on the route
 *
 * @final since 3.8.0
 */
class Route_Voter implements Voter_Interface
{
    public function __construct(private readonly Request_Stack $request_stack)
    {
    }
    public function match_item(Item_Interface $item): ?bool
    {
        $request = $this->request_stack->get_main_request();
        if (null === $request) {
            return null;
        }
        $route = $request->attributes->get('_route');
        if (null === $route) {
            return null;
        }
        $routes = (array) $item->get_extra('routes', []);
        foreach ($routes as $tested_route) {
            if (\is_string($tested_route)) {
                $tested_route = ['route' => $tested_route];
            }
            if (!\is_array($tested_route)) {
                throw new \InvalidArgumentException('Routes extra items must be strings or arrays.');
            }
            if ($this->is_matching_route($request, $tested_route)) {
                return true;
            }
        }
        return null;
    }
    /**
     * @phpstan-param array{route?: string|null, pattern?: string|null, parameters?: array<string, mixed>, query_parameters?: array<string, string>} $testedRoute
     */
    private function is_matching_route(Request $request, array $tested_route): bool
    {
        $route = $request->attributes->get('_route');
        if (isset($tested_route['route'])) {
            if ($route !== $tested_route['route']) {
                return false;
            }
        } elseif (!empty($tested_route['pattern'])) {
            if (!\preg_match($tested_route['pattern'], (string) $route)) {
                return false;
            }
        } else {
            throw new \InvalidArgumentException('Routes extra items must have a "route" or "pattern" key.');
        }
        return $this->is_matching_parameters($request, $tested_route) && $this->is_matching_query_parameters($request, $tested_route);
    }
    /**
     * @phpstan-param array{route?: string|null, pattern?: string|null, parameters?: array<string, mixed>, query_parameters?: array<string, string>} $testedRoute
     */
    private function is_matching_parameters(Request $request, array $tested_route): bool
    {
        if (!isset($tested_route['parameters'])) {
            return true;
        }
        $route_parameters = $request->attributes->get('_route_params', []);
        foreach ($tested_route['parameters'] as $name => $value) {
            // cast both to string so that we handle integer and other non-string parameters, but don't stumble on 0 == 'abc'.
            if (!isset($route_parameters[$name]) || (string) $route_parameters[$name] !== (string) $value) {
                return false;
            }
        }
        return true;
    }
    /**
     * @phpstan-param array{route?: string|null, pattern?: string|null, parameters?: array<string, mixed>, query_parameters?: array<string, string>} $testedRoute
     */
    private function is_matching_query_parameters(Request $request, array $tested_route): bool
    {
        if (!isset($tested_route['query_parameters'])) {
            return true;
        }
        $route_query_parameters = $request->query->all();
        foreach ($tested_route['query_parameters'] as $name => $value) {
            // cast both to string so that we handle integer and other non-string parameters, but don't stumble on 0 == 'abc'.
            if (!isset($route_query_parameters[$name]) || \is_array($route_query_parameters[$name]) || (string) $route_query_parameters[$name] !== (string) $value) {
                return false;
            }
        }
        return true;
    }
}