<?php

declare(strict_types = 1);

namespace Drupal\niklan\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

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
 * @Filter(
 *   id = "niklan_prismjs",
 *   title = @Translation("Prism.js"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 *   weight = 100,
 * )
 */
final class PrismJsHighlighter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $result = new FilterProcessResult($text);
    if (!$this->isContainCode($text)) {
      return $result;
    }

    $result->addAttachments([
      'library' => ['niklan/code-highlight'],
    ]);

    return $result;
  }

  /**
   * Checks is text contains code.
   *
   * This is lite and fast way to ensure the text contains or not <pre> tag.
   *
   * @param string $text
   *   A text to check.
   *
   * @return bool
   *   TRUE if <pre> usage found, FALSE if not.
   */
  protected function isContainCode(string $text): bool {
    return (bool) \stristr($text, '<pre');
  }

}
