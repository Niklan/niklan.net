<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Converter;

use Drupal\external_content\Contract\Converter\MarkupConverterInterface;
use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Provides a foo converter.
 */
final class FooConverter implements MarkupConverterInterface {

  /**
   * {@inheritdoc}
   */
  public function convert(ExternalContentHtml $result): void {
    $content = $result->getContent();
    $content = \str_replace('**foo**', '<strong>foo</strong>', $content);
    $result->setContent($content);
  }

}
