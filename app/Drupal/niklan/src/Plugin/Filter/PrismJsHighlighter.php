<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Filter;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;

/**
 * Provides a 'Prism.js' filter.
 *
 * The filter looking for `<pre>` tag inside text and if founds it, attach
 * library with syntax highlighter library. This way library will be attached
 * on pages where text contains code.
 *
 * Filter used instead of simple preprocess because there are cases when
 * preprocess wont work. F.e. library attached on paragraph type 'code', but
 * content doesn't contains any of such paragraphs, so library wont be attached.
 * But this material can has code to highlight inside comments.
 *
 * @deprecated Remove it.
 */
#[Filter(
  id: 'niklan_prismjs',
  title: new TranslatableMarkup('Prism.js'),
  type: FilterInterface::TYPE_MARKUP_LANGUAGE,
  weight: 100,
)]
final class PrismJsHighlighter extends FilterBase {

  #[\Override]
  public function process($text, $langcode): FilterProcessResult {
    $result = new FilterProcessResult($text);

    if (!\stristr($text, '<pre')) {
      return $result;
    }

    $result->addAttachments(['library' => ['niklan/code-highlight']]);

    return $result;
  }

}
