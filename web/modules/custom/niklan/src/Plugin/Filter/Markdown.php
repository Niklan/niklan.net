<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use League\CommonMark\CommonMarkConverter;

/**
 * Provides a 'Markdown' filter.
 *
 * @Filter(
 *   id = "niklan_markdown",
 *   title = @Translation("Markdown"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 *   weight = -10
 * )
 */
final class Markdown extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $converter = new CommonMarkConverter();
    $rendered_content = $converter->convert($text);

    return new FilterProcessResult($rendered_content->getContent());
  }

}
