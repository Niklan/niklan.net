<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Nodes\Code\CodeContentHtmlParser;
use Drupal\external_content\Nodes\Format\FormatContentHtmlParser;
use Drupal\external_content\Nodes\Heading\HeadingContentHtmlParser;
use Drupal\external_content\Nodes\HtmlElement\HtmlElementContentHtmlParser;
use Drupal\external_content\Nodes\Image\ImageContentHtmlParser;
use Drupal\external_content\Nodes\Link\LinkContentHtmlParser;
use Drupal\external_content\Nodes\List\ListContentHtmlParser;
use Drupal\external_content\Nodes\ListItem\ListItemContentHtmlParser;
use Drupal\external_content\Nodes\Paragraph\ParagraphContentHtmlParser;
use Drupal\external_content\Nodes\Text\TextContentHtmlParser;
use Drupal\external_content\Nodes\ThematicBreak\ThematicBreakContentHtmlParser;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Html\HtmlNodeParser>>
 */
final readonly class DefaultHtmlParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new TextContentHtmlParser());
    $target->add(new ParagraphContentHtmlParser());
    $target->add(new FormatContentHtmlParser());
    $target->add(new HeadingContentHtmlParser());
    $target->add(new ImageContentHtmlParser());
    $target->add(new LinkContentHtmlParser());
    $target->add(new ListContentHtmlParser());
    $target->add(new ListItemContentHtmlParser());
    $target->add(new CodeContentHtmlParser());
    $target->add(new ThematicBreakContentHtmlParser());
    // As a fallback for any other HTML element which is not parsed directly.
    $target->add(new HtmlElementContentHtmlParser(), -100);
  }

}
