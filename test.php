<?php

use Drupal\external_content\Contract\Converter\ExternalContentMarkupConverterInterface;
use Drupal\external_content\Contract\Parser\ExternalContentHtmlParserInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Parser\HtmlElementParser;
use Drupal\external_content\Parser\PlainTextParser;

$env = new Environment(new Configuration());
$env->addHtmlParser(PlainTextParser::class, 100);
$env->addHtmlParser(HtmlElementParser::class);

$content = <<<'HTML'
<p>Hello, World!</p>

<p>Ho about some <strong>and <em>italic</em></strong> content.</p>

<p>Or maybe some <a href="#">link</a>.</p>
HTML;

$html = new ExternalContentHtml(new ExternalContentFile('foo', 'bart'), $content);

$parser = \Drupal::service(ExternalContentHtmlParserInterface::class);
$parser->setEnvironment($env);
$doc = $parser->parse($html);

dump($doc);
