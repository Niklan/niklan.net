<?php

use Drupal\niklan\CommonMark\Extension\NiklanMarkdownExtension;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;

$environment = new Environment();
$environment->addExtension(new NiklanMarkdownExtension());
$converter = new MarkdownConverter($environment);

$markdown = <<<'Markdown'
:::: figure [test] (argument) {#id .class foo=bar}
  :: figcaption [test] (argument) {#id .class .class-b foo=bar baz="test 123"}
::::
Markdown;

dump($converter->convert($markdown)->getContent());
