<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Contract\Parser\ParserContext;
use Psr\Log\LoggerInterface;

final readonly class HtmlParserContext implements ParserContext {

  public function __construct(
    private LoggerInterface $logger,
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

}
