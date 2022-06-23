<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Dto\ElementInterface;
use Drupal\external_content\Dto\HtmlParserStateInterface;
use Drupal\external_content\Dto\PlainTextElement;

/**
 * Provides a plain text parser plugin.
 *
 * This plugin will be used when no other suitable plugins found. This plugin
 * make sure that we always have a result from parsers.
 *
 * @ExternalContentHtmlParser(
 *   id = "plain_text",
 *   label = @Translation("Plain text"),
 *   weight = 1000,
 * )
 */
final class PlainTextParser implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(\DOMNode $node): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMNode $node, HtmlParserStateInterface $html_parser_state): ElementInterface {
    return new PlainTextElement($node->nodeValue);
  }

}
