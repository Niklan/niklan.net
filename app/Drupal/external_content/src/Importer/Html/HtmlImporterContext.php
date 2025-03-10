<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\ImporterContext;
use Psr\Log\LoggerInterface;

final class HtmlImporterContext implements ImporterContext {

  private ?HtmlParser $htmlChildrenParser = NULL;

  public function __construct(
    public string $rawHtmlContent,
    private readonly LoggerInterface $logger,
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

  public function setHtmlChildrenParser(HtmlParser $transformer): void {
    $this->htmlChildrenParser = $transformer;
  }

  public function getHtmlChildrenParser(): HtmlParser {
    if (!$this->htmlChildrenParser) {
      throw new \LogicException("The child HTML parser shouldn't be called before it is set");
    }

    return $this->htmlChildrenParser;
  }

}
