<?php

declare(strict_types=1);

/**
 * Example: Build and render a navigation menu as an HTML list.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;

// 1. Create the factory and build a menu tree.
$factory = new MenuFactory();

$menu = $factory->createItem('root');

$menu->addChild('Home',     ['uri' => '/']);
$menu->addChild('About',    ['uri' => '/about']);
$menu->addChild('Products', ['uri' => '/products']);

$products = $menu->getChild('Products');
$products->addChild('Widget A', ['uri' => '/products/widget-a']);
$products->addChild('Widget B', ['uri' => '/products/widget-b']);

$menu->addChild('Contact', ['uri' => '/contact']);

// 2. Set up a matcher that marks the current request URI as "current".
$currentUri = $_SERVER['REQUEST_URI'] ?? '/products';
$matcher    = new Matcher([new UriVoter($currentUri)]);

// 3. Render to an HTML <ul> list.
$renderer = new ListRenderer($matcher);
echo $renderer->render($menu);
