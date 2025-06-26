<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Nodes\CodeBlock\ArrayParser as CodeParser;
use Drupal\external_content\Nodes\Format\ArrayParser as FormatParser;
use Drupal\external_content\Nodes\Heading\ArrayParser as HeadingParser;
use Drupal\external_content\Nodes\HtmlElement\ArrayParser as HtmlElementParser;
use Drupal\external_content\Nodes\Image\ArrayParser as ImageParser;
use Drupal\external_content\Nodes\Link\ArrayParser as LinkParser;
use Drupal\external_content\Nodes\List\ArrayParser as ListParser;
use Drupal\external_content\Nodes\ListItem\ArrayParser as ListItemParser;
use Drupal\external_content\Nodes\Paragraph\ArrayParser as ParagraphParser;
use Drupal\external_content\Nodes\Text\ArrayParser as TextParser;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Array\ArrayParser>>
 */
final readonly class ArrayExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new CodeParser());
    $target->add(new HeadingParser());
    $target->add(new LinkParser());
    $target->add(new ListParser());
    $target->add(new ListItemParser());
    $target->add(new ParagraphParser());
    $target->add(new TextParser());
    $target->add(new FormatParser());
    $target->add(new ImageParser());
    $target->add(new HtmlElementParser());
  }

}
