<?php declare(strict_types = 1);

namespace Drupal\external_content\Converter;

use Drupal\Component\FrontMatter\FrontMatter;
use Drupal\external_content\Contract\Converter\MarkupPreConverterInterface;
use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Provides a Front Matter extractor before markup convert.
 */
final class FrontMatterExtractor implements MarkupPreConverterInterface {

  /**
   * {@inheritdoc}
   */
  public function preConvert(ExternalContentHtml $result): void {
    $content = $result->getContent();
    $front_matter = FrontMatter::create($content);
    $result->setData('front_matter', $front_matter->getData());
    $result->setContent($front_matter->getContent());
  }

}
