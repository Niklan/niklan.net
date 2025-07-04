<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Contract\Parser\ParserSource;

/**
 * @implements \Drupal\external_content\Contract\Parser\ParserSource<string>
 */
final readonly class HtmlParserSource implements ParserSource {

  public function __construct(
    private string $rawHtml,
  ) {}

  public function getSourceData(): string {
    return $this->rawHtml;
  }

}
