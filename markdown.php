<?php

use Drupal\niklan\CommonMark\Extension\NiklanMarkdownExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
//$environment->addExtension(new NiklanMarkdownExtension());

$converter = new MarkdownConverter($environment);

$test = <<<'Markdown'
---
foo: 'bar'
---

![](https://www.youtube.com/watch?v=Y1I7zGn6F-w)
Markdown;

$result = $converter->convert($test);

dump($result);
