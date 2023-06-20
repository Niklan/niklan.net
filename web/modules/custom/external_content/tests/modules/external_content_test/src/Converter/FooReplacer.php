<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Converter;

use Drupal\external_content\Contract\Converter\MarkupPostConverterInterface;
use Drupal\external_content\Contract\Converter\MarkupPreConverterInterface;
use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Provides a pre-converter that replaces all 'foo' with a 'bar'.
 */
final class FooReplacer implements MarkupPreConverterInterface, MarkupPostConverterInterface {

  /**
   * {@inheritdoc}
   */
  public function preConvert(ExternalContentHtml $result): void {
    $this->doReplace($result);
  }

  /**
   * Replaces 'foo' with a 'bar'.
   *
   * @param \Drupal\external_content\Data\ExternalContentHtml $result
   *   The external content.
   */
  protected function doReplace(ExternalContentHtml $result): void {
    $content = $result->getContent();
    $content = \str_replace('foo', 'bar', $content);
    $result->setContent($content);
  }

  /**
   * {@inheritdoc}
   */
  public function postConvert(ExternalContentHtml $result): void {
    $this->doReplace($result);
  }

}
