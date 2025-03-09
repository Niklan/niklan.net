<?php

declare(strict_types=1);

namespace Drupal\external_content\Transformer\Html;

use Drupal\external_content\Contract\Transformer\TransformerContext;
use Psr\Log\LoggerInterface;

final class HtmlTransformerContext implements TransformerContext {

  public function __construct(
    public string $rawHtmlContent,
    private readonly LoggerInterface $logger,
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

}
