<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Exporter\ExporterContext;
use Drupal\external_content\Contract\Exporter\ExportRequest;
use Drupal\external_content\Nodes\Document;

/**
 * @implements \Drupal\external_content\Contract\Exporter\ExportRequest<\Drupal\external_content\Exporter\Array\ArrayExporterContext>
 */
final class ArrayExportRequest implements ExportRequest {

  public function __construct(
    private Document $content,
    private ExporterContext $context,
    private ArrayBuilder $builder,
  ) {}

  public function getContent(): Document {
    return $this->content;
  }

  /**
   * @return \Drupal\external_content\Exporter\Array\ArrayExporterContext
   */
  public function getContext(): ExporterContext {
    return $this->context;
  }

  public function getArrayStructureBuilder(): ArrayBuilder {
    return $this->builder;
  }

}
