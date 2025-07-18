<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Filter;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;
use League\CommonMark\CommonMarkConverter;

#[Filter(
  id: 'niklan_markdown',
  title: new TranslatableMarkup('Markdown'),
  type: FilterInterface::TYPE_MARKUP_LANGUAGE,
  weight: -10,
)]
final class Markdown extends FilterBase {

  #[\Override]
  public function process($text, $langcode): FilterProcessResult {
    $converter = new CommonMarkConverter();
    $rendered_content = $converter->convert($text);

    return new FilterProcessResult($rendered_content->getContent());
  }

}
