<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Importer\ContentImporterContext;
use Drupal\external_content\Contract\Importer\ContentImporterSource;
use Drupal\external_content\Contract\Importer\ContentImportRequest;

/**
 * @implements \Drupal\external_content\Contract\Importer\ContentImportRequest<\Drupal\external_content\Importer\Array\ArrayContentImporterSource, \Drupal\external_content\Importer\Array\ArrayContentImporterContext>
 */
final readonly class ArrayContentImportRequest implements ContentImportRequest {

  public function __construct(
    private ContentImporterSource $source,
    private ContentImporterContext $context,
    private ArrayParser $parser,
  ) {}

  /**
   * @return \Drupal\external_content\Importer\Array\ArrayContentImporterSource
   */
  public function getSource(): ContentImporterSource {
    return $this->source;
  }

  /**
   * @return \Drupal\external_content\Importer\Array\ArrayContentImporterContext
   */
  public function getContext(): ContentImporterContext {
    return $this->context;
  }

  public function getArrayParser(): ArrayParser {
    return $this->parser;
  }

}
