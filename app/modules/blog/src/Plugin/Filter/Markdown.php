<?php

declare(strict_types=1);

namespace Drupal\app_blog\Plugin\Filter;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;
use League\CommonMark\CommonMarkConverter;

#[Filter(
  id: self::ID,
  title: new TranslatableMarkup('Markdown'),
  type: FilterInterface::TYPE_MARKUP_LANGUAGE,
  weight: -10,
)]
final class Markdown extends FilterBase {

  public const string ID = 'app_blog_markdown';

  #[\Override]
  public function process($text, $langcode): FilterProcessResult {
    $converter = new CommonMarkConverter();
    $rendered_content = $converter->convert($text);

    return new FilterProcessResult($rendered_content->getContent());
  }

}
