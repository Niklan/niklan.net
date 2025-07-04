<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Contract\Parser\ParseRequest;
use Drupal\external_content\Contract\Parser\ParserContext;
use Drupal\external_content\Contract\Parser\ParserSource;

/**
 * @implements \Drupal\external_content\Contract\Parser\ParseRequest<\Drupal\external_content\Parser\Html\HtmlParserSource, \Drupal\external_content\Parser\Html\HtmlParserContext>
 */
final readonly class HtmlParseRequest implements ParseRequest {

  public function __construct(
    private ParserSource $source,
    private ParserContext $context,
    private HtmlParser $htmlParser,
  ) {}

  /**
   * @return \Drupal\external_content\Parser\Html\HtmlParserSource
   */
  public function getSource(): ParserSource {
    return $this->source;
  }

  /**
   * @return \Drupal\external_content\Parser\Html\HtmlParserContext
   */
  public function getContext(): ParserContext {
    return $this->context;
  }

  public function getHtmlParser(): HtmlParser {
    return $this->htmlParser;
  }

}
