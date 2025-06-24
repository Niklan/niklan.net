<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Nodes\Code\CodeContentArrayParser;
use Drupal\external_content\Nodes\Format\FormatContentArrayParser;
use Drupal\external_content\Nodes\Heading\HeadingContentArrayParser;
use Drupal\external_content\Nodes\HtmlElement\HtmlElementContentArrayParser;
use Drupal\external_content\Nodes\Image\ImageContentArrayParser;
use Drupal\external_content\Nodes\Link\LinkContentArrayParser;
use Drupal\external_content\Nodes\List\ListContentArrayParser;
use Drupal\external_content\Nodes\ListItem\ListItemContentArrayParser;
use Drupal\external_content\Nodes\Paragraph\ParagraphContentArrayParser;
use Drupal\external_content\Nodes\Text\TextContentArrayParser;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Array\ArrayElementParser>>
 */
final readonly class DefaultArrayParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new FormatContentArrayParser());
    $target->add(new HeadingContentArrayParser());
    $target->add(new LinkContentArrayParser());
    $target->add(new ListContentArrayParser());
    $target->add(new ListItemContentArrayParser());
    $target->add(new ParagraphContentArrayParser());
    $target->add(new TextContentArrayParser());
    $target->add(new CodeContentArrayParser());
    $target->add(new ImageContentArrayParser());
    $target->add(new HtmlElementContentArrayParser());
  }

}
