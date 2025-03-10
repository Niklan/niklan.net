<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\ImporterContext;
use Drupal\external_content\Contract\Importer\ImporterSource;
use Drupal\external_content\Contract\Importer\ImportRequest;

final readonly class HtmlImportRequest implements ImportRequest {

  public function __construct(
    private ImporterSource $source,
    private ImporterContext $context,
    private HtmlParser $htmlParser,
  ) {}

  public function getSource(): ImporterSource {
    return $this->source;
  }

  public function getContext(): ImporterContext {
    return $this->context;
  }

  public function getHtmlParser(): HtmlParser {
    return $this->htmlParser;
  }

}
