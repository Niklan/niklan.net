<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Importer\Array\Parser\CodeArrayParser;
use Drupal\external_content\Importer\Array\Parser\FormatArrayParser;
use Drupal\external_content\Importer\Array\Parser\HeadingArrayParser;
use Drupal\external_content\Importer\Array\Parser\ImageArrayParser;
use Drupal\external_content\Importer\Array\Parser\LinkArrayParser;
use Drupal\external_content\Importer\Array\Parser\ListArrayParser;
use Drupal\external_content\Importer\Array\Parser\ListItemArrayParser;
use Drupal\external_content\Importer\Array\Parser\ParagraphArrayParser;
use Drupal\external_content\Importer\Array\Parser\TextArrayParser;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Array\ArrayElementParser>>
 */
final readonly class DefaultArrayParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new FormatArrayParser());
    $target->add(new HeadingArrayParser());
    $target->add(new LinkArrayParser());
    $target->add(new ListArrayParser());
    $target->add(new ListItemArrayParser());
    $target->add(new ParagraphArrayParser());
    $target->add(new TextArrayParser());
    $target->add(new CodeArrayParser());
    $target->add(new ImageArrayParser());
  }

}
