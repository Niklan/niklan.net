<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Array;

use Drupal\external_content\Contract\Parser\ParseRequest;
use Drupal\external_content\Contract\Parser\ParserContext;
use Drupal\external_content\Contract\Parser\ParserSource;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Parser\ParseRequest<\Drupal\external_content\Parser\Array\ArrayParserSource, \Drupal\external_content\Parser\Array\ArrayParserContext>
 */
final readonly class ArrayParseRequest implements ParseRequest {

  public function __construct(
    private ParserSource $source,
    private ParserContext $context,
    private Registry $parser,
  ) {}

  /**
   * @return \Drupal\external_content\Parser\Array\ArrayParserSource
   */
  public function getSource(): ParserSource {
    return $this->source;
  }

  /**
   * @return \Drupal\external_content\Parser\Array\ArrayParserContext
   */
  public function getContext(): ParserContext {
    return $this->context;
  }

  public function getArrayParser(): ArrayParser {
    return $this->parser;
  }

}
