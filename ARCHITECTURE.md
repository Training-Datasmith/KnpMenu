# KnpMenu Architecture

## Purpose

A PHP menu-building library that produces navigational structures as PHP objects
and renders them to HTML lists, Twig templates, or any custom format.

## Directory Structure

```
src/Knp/Menu/
  Item_Interface.php            — the core contract for every menu node
  Menu_Item.php                 — concrete implementation of ItemInterface
  Factory_Interface.php         — creates MenuItem trees from raw options arrays
  Menu_Factory.php              — default factory; extensible via extensions
  Node_Interface.php            — node abstraction for tree loading

  Attribute/
    As_Menu_Builder.php         — PHP 8.1 attribute to tag menu builder methods

  Factory/
    Extension_Interface.php     — interface for MenuFactory extensions
    Core_Extension.php          — built-in extension: uri, route, extras, …

  Integration/Symfony/
    Routing_Extension.php       — Symfony Router integration for route URIs

  Iterator/
    Recursive_Item_Iterator.php — RecursiveIterator over the item tree
    Current_Item_Filter_Iterator.php  — yields only the "current" item
    Displayed_Item_Filter_Iterator.php — yields only displayed items

  Loader/
    Loader_Interface.php
    Array_Loader.php            — builds a menu from a PHP array
    Node_Loader.php             — builds a menu from Node objects

  Matcher/
    Matcher_Interface.php
    Matcher.php                 — determines if an item is "current"
    Voter/
      Voter_Interface.php
      Uri_Voter.php             — matches by current request URI
      Route_Voter.php           — matches by Symfony route name / params
      Regex_Voter.php           — matches by URI regex
      Callback_Voter.php        — matches via user-supplied callable

  Provider/
    Menu_Provider_Interface.php
    Array_Access_Provider.php   — resolves menus from an ArrayAccess map
    Chain_Provider.php          — delegates to multiple providers
    Lazy_Provider.php           — lazily builds menus on first access
    Psr_Provider.php            — PSR-11 container-backed provider

  Renderer/
    Renderer_Interface.php
    Renderer.php                — abstract base
    List_Renderer.php           — renders to <ul>/<li> HTML lists
    Twig_Renderer.php           — renders via a Twig template
    Array_Access_Provider.php / Psr_Provider.php — renderer registries

  Twig/
    Helper.php                  — Twig helper (non-template usage)
    Menu_Extension.php          — Twig extension registers functions/filters
    Menu_Runtime_Extension.php  — lazy-loaded Twig runtime

  Util/
    Menu_Manipulator.php        — reorder, slice, and transform menu trees
```

## Key Design Decisions

- **`ItemInterface`-first**: the entire system operates on `ItemInterface`;
  `MenuItem` is the default concrete class but any compatible implementation
  can be used.
- **Factory extensions**: `MenuFactory` delegates option processing to a
  priority-ordered list of `ExtensionInterface` objects, making it easy to add
  custom item options without subclassing the factory.
- **Matcher / Voter**: the "current" state of a menu item is determined by
  injecting one or more `VoterInterface` objects into `Matcher`, cleanly
  separating routing concerns from the menu model.
- **Provider registry**: menus are looked up by name through a
  `MenuProviderInterface`, enabling lazy construction and DI-container
  integration.

## Extension Points

- Implement `ExtensionInterface` to handle custom factory options.
- Implement `VoterInterface` for custom "current item" detection logic.
- Implement `MenuProviderInterface` to source menus from any registry.
- Implement `RendererInterface` for custom output formats.

## Dependency Flow

```
Consumer
  └── MenuFactory::createItem('root', $options)
        └── MenuItem (tree of ItemInterface nodes)
              ├── Matcher (determines current item) → Voter\*
              └── Renderer (List_Renderer / Twig_Renderer)
```
