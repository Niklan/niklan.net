<?php

declare(strict_types=1);

namespace Drupal\external_content_test\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Dto\ElementInterface;
use Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserInterface;
use Drupal\external_content_test\Dto\TestElement;

/**
 * Provides a foo bar HTML parser.
 *
 * @ExternalContentHtmlParser(
 *   id = "foo_bar",
 *   label = @Translation("Foo Bar"),
 * )
 */
final class FooBar implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(\DOMNode $node): bool {
    return $node->nodeName === 'foo-bar';
  }

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMNode $node): ElementInterface {
    return new TestElement($node->nodeValue);
  }

}
