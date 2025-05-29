<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Nodes\Code\CodeHtmlParser;
use Drupal\external_content\Nodes\Format\FormatHtmlParser;
use Drupal\external_content\Nodes\Heading\HeadingHtmlParser;
use Drupal\external_content\Nodes\Image\ImageHtmlParser;
use Drupal\external_content\Nodes\Link\LinkHtmlParser;
use Drupal\external_content\Nodes\List\ListHtmlParser;
use Drupal\external_content\Nodes\ListItem\ListItemHtmlParser;
use Drupal\external_content\Nodes\Paragraph\ParagraphHtmlParser;
use Drupal\external_content\Nodes\Text\TextHtmlParser;
use Drupal\external_content\Nodes\ThematicBreak\ThematicBreakHtmlParser;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Html\HtmlNodeParser>>
 */
final readonly class DefaultHtmlParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new TextHtmlParser());
    $target->add(new ParagraphHtmlParser());
    $target->add(new FormatHtmlParser());
    $target->add(new HeadingHtmlParser());
    $target->add(new ImageHtmlParser());
    $target->add(new LinkHtmlParser());
    $target->add(new ListHtmlParser());
    $target->add(new ListItemHtmlParser());
    $target->add(new CodeHtmlParser());
    $target->add(new ThematicBreakHtmlParser());
  }

}
