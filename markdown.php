<?php

use Drupal\niklan\CommonMark\NiklanMarkdownExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new NiklanMarkdownExtension());

$converter = new MarkdownConverter($environment);

$test = <<<'Markdown'
```php {"highlight_lines":"34:37,42:48","header":"core/lib/Drupal/Core/PathProcessor/PathProcessorAlias.php"}
echo "Hello World!";
```
Markdown;

$result = $converter->convert($test);

dump($result);
