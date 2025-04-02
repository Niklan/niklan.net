<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Importer\Array\Parser\ArrayParser;
use Drupal\external_content\Importer\Array\Parser\CodeArrayParser;
use Drupal\external_content\Importer\Array\Parser\FormatArrayParser;
use Drupal\external_content\Importer\Array\Parser\HeadingArrayParser;
use Drupal\external_content\Importer\Array\Parser\ImageArrayParser;
use Drupal\external_content\Importer\Array\Parser\LinkArrayParser;
use Drupal\external_content\Importer\Array\Parser\ListArrayParser;
use Drupal\external_content\Importer\Array\Parser\ListItemArrayParser;
use Drupal\external_content\Importer\Array\Parser\ParagraphArrayParser;
use Drupal\external_content\Importer\Array\Parser\TextArrayParser;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Importer\Array\Parser\ArrayParser>
 */
final readonly class DefaultArrayParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof ArrayParser);
    $target->addParser(new FormatArrayParser());
    $target->addParser(new HeadingArrayParser());
    $target->addParser(new LinkArrayParser());
    $target->addParser(new ListArrayParser());
    $target->addParser(new ListItemArrayParser());
    $target->addParser(new ParagraphArrayParser());
    $target->addParser(new TextArrayParser());
    $target->addParser(new CodeArrayParser());
    $target->addParser(new ImageArrayParser());
  }

}
