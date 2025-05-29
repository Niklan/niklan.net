<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Nodes\Code\CodeArrayParser;
use Drupal\external_content\Nodes\Format\FormatArrayParser;
use Drupal\external_content\Nodes\Heading\HeadingArrayParser;
use Drupal\external_content\Nodes\Image\ImageArrayParser;
use Drupal\external_content\Nodes\Link\LinkArrayParser;
use Drupal\external_content\Nodes\List\ListArrayParser;
use Drupal\external_content\Nodes\ListItem\ListItemArrayParser;
use Drupal\external_content\Nodes\Paragraph\ParagraphArrayParser;
use Drupal\external_content\Nodes\Text\TextArrayParser;
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
