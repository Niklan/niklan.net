<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\ImporterContext;
use Drupal\external_content\Contract\Importer\ImporterSource;
use Drupal\external_content\Contract\Importer\ImportRequest;
use Drupal\external_content\Importer\Html\Parser\HtmlParser;

/**
 * @implements \Drupal\external_content\Contract\Importer\ImportRequest<\Drupal\external_content\Importer\Html\HtmlImporterSource, \Drupal\external_content\Importer\Html\HtmlImporterContext>
 */
final readonly class HtmlImportRequest implements ImportRequest {

  public function __construct(
    private ImporterSource $source,
    private ImporterContext $context,
    private HtmlParser $htmlParser,
  ) {}

  /**
   * @return \Drupal\external_content\Importer\Html\HtmlImporterSource
   */
  public function getSource(): ImporterSource {
    return $this->source;
  }

  /**
   * @return \Drupal\external_content\Importer\Html\HtmlImporterContext
   */
  public function getContext(): ImporterContext {
    return $this->context;
  }

  public function getHtmlParser(): HtmlParser {
    return $this->htmlParser;
  }

}
