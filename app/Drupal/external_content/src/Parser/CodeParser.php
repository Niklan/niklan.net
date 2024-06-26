<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Contract\Parser\ChildHtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\Code;

/**
 * Provides a parser for a <code> element.
 *
 * PHP DOM follows code children and process them as well. The expected behavior
 * for content is using value as is.
 */
final class CodeParser implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parseNode(\DOMNode $node, ChildHtmlParserInterface $child_parser): HtmlParserResult {
    if (!$node instanceof \DOMElement || $node->tagName !== 'code') {
      return HtmlParserResult::pass();
    }

    return HtmlParserResult::replace(new Code($node->textContent));
  }

}
