<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Nodes\CodeBlock\RenderArrayBuilder as CodeBlockBuilder;
use Drupal\external_content\Nodes\Format\RenderArrayBuilder as FormatBuilder;
use Drupal\external_content\Nodes\Heading\RenderArrayBuilder as HeadingBuilder;
use Drupal\external_content\Nodes\HtmlElement\RenderArrayBuilder as HtmlElementBuilder;
use Drupal\external_content\Nodes\Image\RenderArrayBuilder as ImageBuilder;
use Drupal\external_content\Nodes\Link\RenderArrayBuilder as LinkBuilder;
use Drupal\external_content\Nodes\Paragraph\RenderArrayBuilder as ParagraphBuilder;
use Drupal\external_content\Nodes\Text\RenderArrayBuilder as TextBuilder;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Exporter\RenderArray\Builder>>
 */
final class RenderArrayExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new TextBuilder());
    $target->add(new ParagraphBuilder());
    $target->add(new FormatBuilder());
    $target->add(new CodeBlockBuilder());
    $target->add(new HeadingBuilder());
    $target->add(new HtmlElementBuilder());
    $target->add(new ImageBuilder());
    $target->add(new LinkBuilder());
  }

}
