<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Nodes\Code\HtmlParser as CodeParser;
use Drupal\external_content\Nodes\Format\HtmlParser as FormatParser;
use Drupal\external_content\Nodes\Heading\HtmlParser as HeadingParser;
use Drupal\external_content\Nodes\HtmlElement\HtmlParser as HtmlElementParser;
use Drupal\external_content\Nodes\Image\HtmlParser as ImageParser;
use Drupal\external_content\Nodes\Link\HtmlParser as LinkParser;
use Drupal\external_content\Nodes\List\HtmlParser as ListParser;
use Drupal\external_content\Nodes\ListItem\HtmlParser as ListItemParser;
use Drupal\external_content\Nodes\Paragraph\HtmlParser as ParagraphParser;
use Drupal\external_content\Nodes\Text\HtmlParser as TextParser;
use Drupal\external_content\Nodes\ThematicBreak\HtmlParser as ThematicBreakParser;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Html\HtmlNodeParser>>
 */
final readonly class HtmlExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new TextParser());
    $target->add(new ParagraphParser());
    $target->add(new FormatParser());
    $target->add(new CodeParser());
    $target->add(new ImageParser());
    $target->add(new LinkParser());
    $target->add(new ListParser());
    $target->add(new ListItemParser());
    $target->add(new HeadingParser());
    $target->add(new ThematicBreakParser());
    // As a fallback for any other HTML element which is not parsed directly.
    $target->add(new HtmlElementParser(), -100);
  }

}
