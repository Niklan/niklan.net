<?php

use Drupal\niklan\CommonMark\Extension\NiklanMarkdownExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

$environment = new Environment();
$environment->addExtension(new NiklanMarkdownExtension());

$converter = new MarkdownConverter($environment);

$test = <<<'Markdown'
:::::: foo
  ::::: bar
    :::: baz
    ::::
  :::::
::::::
Markdown;

$result = $converter->convert($test)->getContent();

dump($result);
