# External Content

<img src="assets/img/logo.svg" alt="External Content" width="128" align="right">

**External Content** lets you organize content synchronization from external
sources, mostly files. It provides you a set of tools to find, parse, bundle
content, then load them into Drupal and render in sensible way using render
arrays.

## Concept

### Configuration

Configuration lets you pass additional information into environment that can
be accessed by all processor on any stage.

An example of configuration:

```php
$configuration = new Configuration('working_dirs', [
  'public://content',
  'private://content',
]);
```

### Environment

Environment lets you build a specific set of processors with different
priorities, configuration and control how to process them.

### Find

Finder lets you find source files which represents the content outside Drupal.
Content can be anywhere, it is responsibility of finder provide that
information which will be used later for processing files itself.

## Pipeline

1. [x] Find
2. [x] Convert markup into HTML
3. [x] Parse HTML
4. [x] Bundle multiple variants of the same content.
5. [ ] Render array builder
6. [ ] Loader
