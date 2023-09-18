# External Content

<img src="assets/img/logo.svg" alt="External Content" width="128" align="right">

**External Content** lets you organize content synchronization from external
sources, mostly files. It provides you a set of tools to find, parse, bundle
content, then load them into Drupal and render in sensible way using render
arrays.

## Concept

### Finder

Finder lets you find source files which represents the content outside Drupal.
Content can be anywhere, it is responsibility of finder provide that
information which will be used later for processing files itself.

### HTML parser

HTML parser lets you parse content HTML into more simplistic object for future
usage. Later, result of HTML parser can be used with render array builder to
convert back into required HTML.

### Bundler

Bundler lets you bundle multiple different found contents into a single
bundle.

Basically, if you have same content presented into different variations, this
should be a bundle.

E.g. you have content about «Drupal Hooks» which is exists in two different
languages 'ru' and 'en'. This means this is a bundle with two different
variations. It's not limited to a 'language' only, it can be more complex if
needed by setting different attributes for variations inside a bundle.

### Loader

### Render array builder

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

