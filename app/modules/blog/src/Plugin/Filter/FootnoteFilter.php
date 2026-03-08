<?php

declare(strict_types=1);

namespace Drupal\app_blog\Plugin\Filter;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;

#[Filter(
  id: self::ID,
  title: new TranslatableMarkup('Blog footnotes'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  description: new TranslatableMarkup('Attaches footnote tooltip library when footnotes are detected.'),
  weight: 4,
)]
final class FootnoteFilter extends FilterBase {

  public const string ID = 'app_blog_footnote';

  #[\Override]
  public function process($text, $langcode): FilterProcessResult {
    $result = new FilterProcessResult($text);

    if (\str_contains($text, 'footnote-ref')) {
      $result->addAttachments(['library' => ['app_blog/footnote.tooltip']]);
    }

    return $result;
  }

}
