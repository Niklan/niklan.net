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
  title: new TranslatableMarkup('Code highlight'),
  type: FilterInterface::TYPE_MARKUP_LANGUAGE,
  description: new TranslatableMarkup('Adds code highlight library if the pre element is detected.'),
  weight: 100,
)]
final class CodeHighlight extends FilterBase {

  public const string ID = 'app_blog_code_highlight';

  #[\Override]
  public function process($text, $langcode): FilterProcessResult {
    $result = new FilterProcessResult($text);

    if (!\stristr($text, '<pre')) {
      return $result;
    }

    $result->addAttachments(['library' => ['app_blog/hljs']]);

    return $result;
  }

}
