<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Importer\ImporterContext;
use Drupal\external_content\Contract\Importer\ImporterSource;
use Drupal\external_content\Contract\Importer\ImportRequest;

/**
 * @implements \Drupal\external_content\Contract\Importer\ImportRequest<\Drupal\external_content\Importer\Array\ArrayImporterSource, \Drupal\external_content\Importer\Array\ArrayImporterContext>
 */
final readonly class ArrayImportRequest implements ImportRequest {

  public function __construct(
    private ImporterSource $source,
    private ImporterContext $context,
    private ArrayParser $parser,
  ) {}

  /**
   * @return \Drupal\external_content\Importer\Array\ArrayImporterSource
   */
  public function getSource(): ImporterSource {
    return $this->source;
  }

  /**
   * @return \Drupal\external_content\Importer\Array\ArrayImporterContext
   */
  public function getContext(): ImporterContext {
    return $this->context;
  }

  public function getArrayParser(): ArrayParser {
    return $this->parser;
  }

}
