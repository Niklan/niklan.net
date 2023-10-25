<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\PlainText;

/**
 * Provides a parser for a simple text inside HTML elements.
 *
 * It is important that this parser have higher priority over HtmlElementParser.
 */
final class PlainTextParser implements ParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(SourceInterface $source): HtmlParserResult {
    if (!$source instanceof \DOMText) {
      return HtmlParserResult::continue();
    }

    // If this is a DOMText and trim is an empty value, skip processing element,
    // because it's just a whitespace.
    if (!\trim($source->nodeValue)) {
      return HtmlParserResult::stop();
    }

    return HtmlParserResult::finalize(new PlainText($source->nodeValue));
  }

}
