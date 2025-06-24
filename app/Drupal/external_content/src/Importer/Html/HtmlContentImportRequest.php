<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\ContentImporterContext;
use Drupal\external_content\Contract\Importer\ContentImporterSource;
use Drupal\external_content\Contract\Importer\ContentImportRequest;

/**
 * @implements \Drupal\external_content\Contract\Importer\ContentImportRequest<\Drupal\external_content\Importer\Html\HtmlContentImporterSource, \Drupal\external_content\Importer\Html\HtmlContentImporterContext>
 */
final readonly class HtmlContentImportRequest implements ContentImportRequest {

  public function __construct(
    private ContentImporterSource $source,
    private ContentImporterContext $context,
    private HtmlParser $htmlParser,
  ) {}

  /**
   * @return \Drupal\external_content\Importer\Html\HtmlContentImporterSource
   */
  public function getSource(): ContentImporterSource {
    return $this->source;
  }

  /**
   * @return \Drupal\external_content\Importer\Html\HtmlContentImporterContext
   */
  public function getContext(): ContentImporterContext {
    return $this->context;
  }

  public function getHtmlParser(): HtmlParser {
    return $this->htmlParser;
  }

}
