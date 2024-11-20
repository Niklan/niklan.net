<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Filter;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;

#[Filter(
  id: 'niklan_code_highlight',
  title: new TranslatableMarkup('Code highlight'),
  type: FilterInterface::TYPE_MARKUP_LANGUAGE,
  description: new TranslatableMarkup('Adds code highlight library if the pre element is detected.'),
  weight: 100,
)]
final class CodeHighlight extends FilterBase {

  #[\Override]
  public function process($text, $langcode): FilterProcessResult {
    $result = new FilterProcessResult($text);

    if (!\stristr($text, '<pre')) {
      return $result;
    }

    $result->addAttachments(['library' => ['niklan/hljs']]);

    return $result;
  }

}
