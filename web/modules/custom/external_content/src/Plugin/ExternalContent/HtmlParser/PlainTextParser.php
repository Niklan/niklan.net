<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\HtmlParser;

use Drupal\Core\Plugin\PluginBase;
use Drupal\external_content\Contract\ElementInterface;
use Drupal\external_content\Contract\HtmlParserPluginInterface;
use Drupal\external_content\Contract\HtmlParserStateInterface;
use Drupal\external_content\Data\PlainTextElement;

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
final class PlainTextParser extends PluginBase implements HtmlParserPluginInterface {

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
